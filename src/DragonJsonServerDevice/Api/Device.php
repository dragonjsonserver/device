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
	 * @session
	 */
	public function linkAccount($platform = 'browser', array $credentials = ['browser_id' => ''])
	{
		$serviceManager = $this->getServiceManager();
		
		$sessionService = $serviceManager->get('Session');
		$session = $sessionService->getSession();
		$account = $serviceManager->get('Account')->getAccount($session->getAccountId());
		$device = $serviceManager->get('Device')->linkAccount($account, $platform, $credentials);
		$data = $session->getData();
		$data['device'] = $device->toArray();
		$session->setData($data);
		$sessionService->updateSession($session);
	}
	
    /**
	 * Entfernt die aktuelle Deviceverknüpfung für den Account
	 * @session
     * @throws \DragonJsonServer\Exception
	 */
	public function unlinkAccount()
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
		$serviceDevice->unlinkAccount($device);
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
	public function loginAccount($platform = 'browser', array $credentials = ['browser_id' => ''])
	{
		$serviceManager = $this->getServiceManager();

		$device = $serviceManager->get('Device')->getDeviceByPlatformAndCredentials($platform, $credentials);
		$account = $serviceManager->get('Account')->getAccount($device->getAccountId());
		$serviceSession = $serviceManager->get('Session');
		$session = $serviceSession->createSession($account, ['device' => $device->toArray()]);
		$serviceSession->setSession($session);
		return $session->toArray();
	}
	
    /**
	 * Gibt die Einstellungen der Deviceplattformen zurück
	 * @return array
	 */
	public function getDeviceplatforms()
	{
        return $this->getServiceManager()->get('Config')['deviceplatforms'];
	}
}
