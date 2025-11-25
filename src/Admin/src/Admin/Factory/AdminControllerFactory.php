<?php
/**
 * Zend Framework (http://framework.zend.com/)
 *
 * @link      http://github.com/zendframework/ZendSkeletonApplication for the canonical source repository
 * @copyright Copyright (c) 2005-2015 Zend Technologies USA Inc. (http://www.zend.com)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 */

namespace Admin\Factory;
 
 use Admin\Controller\AdminRosanController;
 use Interop\Container\ContainerInterface;
 use Zend\ServiceManager\FactoryInterface;
 use Zend\ServiceManager\ServiceLocatorInterface;
 
class AdminControllerFactory implements FactoryInterface
{
//	public function __invoke(ContainerInterface $container, $requestedName, array $options = null)
    public function createService(ServiceLocatorInterface $serviceLocator)
    {
    	error_log("factory");
        $credentialCallback = function ($passwordInDatabase, $passwordProvided) {
            //error_log("hello from credentialCallback");
            //error_log($passwordProvided);
            //error_log(password_hash($passwordProvided, PASSWORD_DEFAULT));
            //error_log($passwordInDatabase);
            return password_verify($passwordProvided, $passwordInDatabase);
        };

        $container = $serviceLocator->getServiceLocator();
        $dbadapter = $container->get('Zend\Db\Adapter\Adapter');
        $adapter = new \Zend\Authentication\Adapter\DbTable\CallbackCheckAdapter(        
                                            $container->get('Zend\Db\Adapter\Adapter'),
                                            'dic_users', // Table name
                                            'username', // Identity column
                                            'password', // Credential column
                                            $credentialCallback // This adapter will run this function in order to check the password
        );

        // Create the storage adapter
        $storage = new \Zend\Authentication\Storage\Session();

        // Finally create the service
        $authService = new \Zend\Authentication\AuthenticationService($storage, $adapter);  
        return new AdminRosanController($container, $authService);
 //       return new $requestedName($container, $authService);
    }
}

?>