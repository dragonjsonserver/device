<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServerDevice
 */

namespace DragonJsonServerDevice\Api;

/**
 * API Klasse zur Verwaltung von Deviceverknüpfungen
 */
class Device
{
	use \DragonJsonServer\ServiceManagerTrait;

	/**
	 * Erstellt eine neue Deviceverknüpfung für den Account
	 * @param string $platform
	 * @param object $credentials
	 * @DragonJsonServerAccount\Annotation\Session
	 */
	public function createDevice($platform = 'browser', array $credentials = ['browser_id' => ''])
	{
		$serviceManager = $this->getServiceManager();
		
		$sessionService = $serviceManager->get('Session');
		$session = $sessionService->getSession();
		$device = $serviceManager->get('Device')->createDevice($session->getAccountId(), $platform, $credentials);
		$data = $session->getData();
		$data['device'] = $device->toArray();
		$session->setData($data);
		$sessionService->updateSession($session);
	}
	
    /**
	 * Entfernt die aktuelle Deviceverknüpfung für den Account
     * @throws \DragonJsonServer\Exception
	 * @DragonJsonServerAccount\Annotation\Session
	 */
	public function removeDevice()
	{
		$serviceManager = $this->getServiceManager();

		$sessionService = $serviceManager->get('Session');
		$session = $sessionService->getSession();
		$data = $session->getData();
		if (!isset($data['device'])) {
			throw new \DragonJsonServer\Exception('missing device in session', ['session' => $session->toArray()]);
		}
		$serviceDevice = $serviceManager->get('Device');
		$device = $serviceDevice->getDeviceById($data['device']['device_id']);
		$serviceDevice->removeDevice($device);
		unset($data['device']);
		$session->setData($data);
		$sessionService->updateSession($session);
	}
	
    /**
	 * Meldet den Account mit der übergebenen Deviceverknüpfung an
	 * @param string $platform
	 * @param object $credentials
	 * @return array
	 */
	public function loginDevice($platform = 'browser', array $credentials = ['browser_id' => ''])
	{
		$serviceManager = $this->getServiceManager();

		$device = $serviceManager->get('Device')->getDeviceByPlatformAndCredentials($platform, $credentials);
		$serviceSession = $serviceManager->get('Session');
		$session = $serviceSession->createSession($device->getAccountId(), ['device' => $device->toArray()]);
		$serviceSession->setSession($session);
		return $session->toArray();
	}
	
    /**
	 * Gibt die Einstellungen der Deviceplattformen zurück
	 * @return array
	 */
	public function getDeviceplatforms()
	{
        return $this->getServiceManager()->get('Config')['dragonjsonserverdevice']['deviceplatforms'];
	}
}
