<?php
/**
 * Lagged_Application
 *
 * PHP Version 5
 *
 * @category Core
 * @package  Lagged_Application
 * @author   Till Klampaeckel <klampaeckel@lagged.de>
 * @version  SVN: $Id$
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @link     http://www.lagged.de/
 */

// Set the include path
set_include_path(
    dirname(__FILE__) . '/library'
    . PATH_SEPARATOR
    . dirname(__FILE__) . '/app'
    . PATH_SEPARATOR
    . get_include_path()
);

/**
 * In order to use this, please create an index.php router with the following:
 *
 * <code>
 * <?php
 * include '/path/to/bootstrap.php';
 * $app = new Lagged_Application('production');
 * $app->start();
 * Zend_Controller_Front::getInstance()->dispatch();
 * </code>
 *
 * Lagged_Application also expects a config.ini in app/etc/ with settings for DB,
 * cookies, email, etc. and the various sections -- e.g. production, staging and 
 * testing -- see below.
 *
 * app/etc/config.ini:
 * [production]
 * db.params.username = root
 * db.params.password =
 * db.params.host     = localhost
 * db.params.port     = 3306
 * db.params.dbname   = mysql
 *
 * cookie.name   = lagged
 * cookie.path   = /
 * cookie.domain = .localhost
 *
 * email.auth = 1
 * email.user = user@example.org
 * email.pass = user
 * email.host = smtp.example.org
 *
 * [testing : production]
 *
 * Last but not least, you will also need to take care of autoload, at the minimum:
 *
 * <code>
 * <?php
 * require_once 'Zend/Loader.php';
 * function __autoload($className) {
 *     // this is slow ;-)
 *     Zend_Loader::loadClass($className);
 * }
 * </code>
 *
 * PHP-wise -- a standard PHP 5.2.x install with the APC extension.
 */

/**
 * Lagged_Application
 * 
 * Initializes the website, loads necessary classes and creates objects.
 * 
 * @category Core
 * @package  Lagged_Application
 * @author   Till Klampaeckel <klampaeckel@lagged.de>
 * @license  http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version  Release: @package_version@
 * @link     http://www.lagged.de/
 * @final
 */
final class Lagged_Application
{
    /**
     * The environment state of your current application
     *
     * @var string $_environment Use in {@link app/etc/config.ini}
     */
    private $_environment;

    /**
     * @var Zend_Acl
     */
    private $_acl;

    /**
     * @var Zend_Auth
     */
    private $_auth;

    /**
     * __construct
     *
     * @param string $environment production, staging, testing, ...
     *
     * @return Lagged_Application
     * @uses   self::setEnvironment()
     */
    public function __construct($environment = 'production')
    {
        $this->setEnvironment($environment);
    }

    /**
     * Sets the environment to load from configuration file
     *
     * @param string $environment The environment to set.
     * 
     * @return void
     * @uses   self::$_environment
     */
    public function setEnvironment($environment)
    {
        $this->_environment = $environment;
    }

    /**
     * Returns the environment which is currently set
     *
     * @return string
     */
    public function getEnvironment()
    {
        return $this->_environment;
    }

    /**
     * Convenience method to bootstrap the application
     *
     * @return mixed
     * @throws Zend_Exception if the environment is not set.
     */
    public function start()
    {
        if (!$this->_environment) {
            throw new Zend_Exception('Please set the environment using ::setEnvironment');
        }

        $frontController = $this->initialize();
        $this->setupRoutes($frontController);
    }

    /**
     * _readConfig pulls the {@link app/etc/config.ini}.
     * 
     * @return Zend_Config_Ini
     * @uses   Zend_Registry
     */
    private function _readConfig()
    {
        if (($conf = apc_fetch('mainconfig')) !== false) {
            Zend_Registry::set('config', $conf);
            return $conf;
        }
        /*
         * Load the given stage from our configuration file,
         * and store it into the registry for later usage.
         */
        $_root  = dirname(__FILE__);
        $config = new Zend_Config_Ini($_root . '/app/etc/config.ini',
            $this->getEnvironment(), true);

        $config->root = $_root;

        Zend_Registry::set('config', $config);
        apc_store('mainconfig', $config);

        return $config;
    }
    
    /**
     * Initialize all session and authentication related.
     * 
     * In case of 'testing', we don't use the DB session handler.
     * 
     * @param Zend_Config $session Instance of Zend_Config
     * 
     * @return void
     * @uses   Zend_Session
     * @uses   Zend_Auth
     * @uses   Zend_Acl
     * @uses   Zend_Session_Namespace
     */
    private function _initSession(Zend_Config $config)
    {
        $this->_auth = Zend_Auth::getInstance();

        if ($this->_environment != 'testing') {

            // set database options for the session
            // @todo NEED TO OPENSOURCE THIS
            //$sessionManager = new Lagged_Session_Manager();
            //Zend_Session::setSaveHandler($sessionManager);

        }

        // set general options
        Zend_Session::setOptions(array(
            'gc_probability'      => 1,
            'gc_divisor'          => 5000,
            'gc_maxlifetime'      => 259200,

            //'remember_me_seconds' => 259200,                
            //'use_only_cookies'    => 'off',

            'name'                => $config->cookie->name,

            'use_cookies'         => 'on',

            'cookie_lifetime'     => 259200,
            'cookie_path'         => $config->cookie->path,
            'cookie_domain'       => $config->cookie->domain,
            'cookie_secure'       => 'off',
            'save_path'           => $config->root . '/var/session',
        ));

        $this->_auth->setStorage(new Zend_Auth_Storage_Session('Lagged_Session'));

        $this->_acl = new Zend_Acl($this->_auth);

        if ($this->getEnvironment() == 'testing') {
            $laggedSession = new Zend_Session_Namespace('Lagged');
        } else {
            $laggedSession = new Zend_Session_Namespace('Lagged', true); // lock
        }

        Zend_Registry::set('laggedSession', $laggedSession);
    }

    /**
     * Initialize the database, parse settings from config.ini and set them all
     * up, by e.g. assigning a default adapter for all models, etc.. All created
     * objects are pushed into the registry.
     * 
     * @param Zend_Config_Ini $config An instance of Zend_Config_Ini
     *
     * @return void
     * @uses   Zend_Db
     * @uses   Zend_Db_Table_Abstract
     * @uses   Zend_Registry
     */
    private function _initDb(Zend_Config_Ini $config)
    {
        try {
            // Zend Db
            $db = Zend_Db::factory($config->db);
            $db->setFetchMode(Zend_Db::FETCH_ASSOC);

            Zend_Db_Table_Abstract::setDefaultAdapter($db);
            Zend_Registry::set('db', $db);

            // setup a cache for all models based on Zend_Db_Table
            if ($this->_environment !== 'testing') {
                $frontendOptions = array('automatic_serialization' => true);
                $backendOptions  = array();
                $cache = Zend_Cache::factory(
                    'Core',
                    'APC',
                    $frontendOptions,
                    $backendOptions
                );
                Zend_Db_Table_Abstract::setDefaultMetadataCache($cache);
            }

        } catch (Zend_Exception $e) {
            echo '<h1>A DB error occured</h1>';
            echo 'We are working on it, check back in a few minutes.';
            if ($this->_environment !== 'production') {
                echo "<!-- {$e->getMessage()} -->";
            }
            exit;
        }
    }

    /**
     * Setup an object for email.
     * 
     * @param Zend_Config_Ini $config An instance of Zend_Config_Ini
     *
     * @return void
     * @uses   Zend_Mail_Transport_Smtp
     * @uses   Zend_Mail
     * @uses   Zend_Registry
     */
    private function _initMail(Zend_Config_Ini $config)
    {
        if ($this->_environment == 'testing') {
            return;
        }
        if ($config->email->auth === '1') {
            $transport = new Zend_Mail_Transport_Smtp($config->email->host,
                array('auth' => 'login',
                    'username' => $config->email->user,
                    'password' => $config->email->pass
            ));
            Zend_Mail::setDefaultTransport($transport);
        }
        $mail = new Zend_Mail();
        Zend_Registry::set('mail', $mail);
    }

    /**
     * Initialize/setup the view object.
     * 
     * @param Zend_Config_Ini $config An instance of Zend_Config_Ini
     *
     * @return void
     * @uses   Zend_View
     * @uses   Zend_Registry
     * @uses   Zend_Controller_Action_HelperBroker
     */
    private function _initView(Zend_Config_Ini $config)
    {
        /**
         * Create a *custom* view object with modified paths,
         * and store it into the registry for later usage.
         */
        $view = new Zend_View();
        $view->setScriptPath($config->root . '/app/views/scripts');
        $view->setHelperPath($config->root . '/app/views/helpers', 'Lagged_View_Helper');
        Zend_Registry::set('view', $view);

        // Add the custom view object to the ViewRenderer
        $viewRenderer = new Zend_Controller_Action_Helper_ViewRenderer();
        $viewRenderer->setView($view);
        Zend_Controller_Action_HelperBroker::addHelper($viewRenderer);

        /**
         * Store the status in APC.
         */
        if (!($status_message = apc_fetch('status_message'))) {
            $statusObj      = new Model_Misc_Status();
            $status_message = $statusObj->getStatus();
            apc_store('status_message', $status_message, 3600);
        }
        
        $viewRenderer->view->assign('status_message', $status_message);
    }

    /**
     * Initialization stage, loads configration files, sets up includes paths
     * and instantiazed the frontController
     *
     * @return Zend_Controller_Front
     * @see    self::bootstrap()
     * @uses   self::_readConfig()
     * @uses   self::_initDb()
     * @uses   self::_initMail()
     * @uses   self::_initView()
     * @uses   self::_initSession()
     */
    public function initialize()
    {
        /**
         * Initialize all the necessary objects.
         */
        $config = $this->_readConfig();

        $this->_initDb($config);
        $this->_initMail($config);
        $this->_initView($config);
        $this->_initSession($config);
        
        /*
         * Create an instance of the frontcontroller, and point it to our
         * controller directory.
         * 
         * Also adds general parameter to all requests.
         * 
         * @see Zend_Auth
         * @see Zend_Acl
         */
        $frontController = Zend_Controller_Front::getInstance();
        $frontController->throwExceptions((bool) $config->mvc->exceptions);

        $frontController->setControllerDirectory($config->root . '/app/controllers', 'default');

        $frontController->registerPlugin(new Zend_Auth($this->_auth, $this->_acl));
        $frontController->setParam('auth', $this->_auth);
        $frontController->setParam('baseUrl', $config->baseUrl);

        return $frontController;
    }

    /**
     * Sets up the custom routes
     *
     * @param Zend_Controller_Front $frontController - The frontcontroller
     *
     * @return Zend_Controller_Router
     */
    public function setupRoutes(Zend_Controller_Front $frontController)
    {
        // Retrieve the router from the frontcontroller
        $router = $frontController->getRouter();

        $router->addRoute('/dev/random',
            new Zend_Controller_Router_Route('/dev/random',
                array('controller' => 'index',
                    'action' => 'devrandom',
                    'module' => 'default')));

        return $router;
    }
}
