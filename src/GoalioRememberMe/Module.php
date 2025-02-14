<?php
namespace GoalioRememberMe;

use Laminas\Mvc\MvcEvent;
use Laminas\Http\Request as HttpRequest;
use Laminas\Loader\StandardAutoloader;
use Laminas\Loader\AutoloaderFactory;
use Laminas\EventManager\EventInterface;

class Module {

    public function getAutoloaderConfig() {
        return array(
            AutoloaderFactory::STANDARD_AUTOLOADER => array(
                StandardAutoloader::LOAD_NS => array(
                    __NAMESPACE__ => __DIR__,
                ),
            ),
        );
    }

    public function getConfig() {
        return include __DIR__ . '/../../config/module.config.php';
    }

    public function getServiceConfig() {
        return array(
            'invokables' => array(
                'GoalioRememberMe\Form\Login'                    => 'GoalioRememberMe\Form\Login',
            ),

            'factories' => array(

                'GoalioRememberMe\Authentication\Adapter\Cookie' => function ($sm) {
                    $service = new Authentication\Adapter\Cookie;
                    $service->setServiceManager($sm);
                    return $service;
                },

                'goaliorememberme_rememberme_service' => function ($sm) {
                    $service = new Service\RememberMe;
                    $service->setServiceManager($sm);
                    return $service;
                },

                'goaliorememberme_module_options' => function ($sm) {
                    $config = $sm->get('Config');
                    return new Options\ModuleOptions(isset($config['goaliorememberme']) ? $config['goaliorememberme'] : array());
                },

                'goaliorememberme_rememberme_mapper' => function ($sm) {
                    $options = $sm->get('lmcuser_module_options');
                    $rememberOptions = $sm->get('goaliorememberme_module_options');
                    $mapper = new Mapper\RememberMe;
                    $mapper->setDbAdapter($sm->get('lmcuser_laminas_db_adapter'));
                    $entityClass = $rememberOptions->getRememberMeEntityClass();
                    $mapper->setEntityPrototype(new $entityClass);
                    $mapper->setHydrator(new Mapper\RememberMeHydrator());
                    return $mapper;
                },

                'lmcuser_login_form' => function($sm) {
                    $options = $sm->get('lmcuser_module_options');
                    $form = new Form\Login(null, $options);
                    $form->setInputFilter(new \LmcUser\Form\LoginFilter($options));
                    return $form;
                },
            ),
        );
    }


    public function onBootstrap(MvcEvent $e) 
    {

        if (!$e->getRequest() instanceof HttpRequest) {
            return;
        }

        $app = $e->getApplication();
        $serviceManager = $app->getServiceManager();

        $userIsLoggedIn = $serviceManager->get('lmcuser_auth_service')->hasIdentity();
        $cookie = $e->getRequest()->getCookie();

        // do autologin only if not done before and cookie is present
        if(!$userIsLoggedIn && isset($cookie['remember_me'])) {
            $adapter = $e->getApplication()->getServiceManager()->get('Lmcuser\Authentication\Adapter\AdapterChain');
            $adapter->prepareForAuthentication($e->getRequest());
            $authService = $e->getApplication()->getServiceManager()->get('lmcuser_auth_service');

            $auth = $authService->authenticate($adapter);
        }

        $app->getEventManager()->getSharedManager()->attach('Lmcuser\Service\User', 'changePassword.post', function(EventInterface $e) use ($serviceManager) {
            $userId = $serviceManager->get('lmcuser_auth_service')->getIdentity()->getId();
            $serviceManager->get('goaliorememberme_rememberme_mapper')->removeAll($userId);
        });
    }
}

