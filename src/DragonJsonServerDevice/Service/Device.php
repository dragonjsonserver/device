<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServerDevice
 */

namespace DragonJsonServerDevice\Service;

/**
 * Serviceklasse zur Verwaltung einer E-Mail Adressverknüpfung
 */
class Device
{
    use \DragonJsonServer\ServiceManagerTrait;
	use \DragonJsonServerDoctrine\EntityManagerTrait;
	
    /**
	 * Erstellt eine neue Deviceverknüpfung für den Account
	 * @param \DragonJsonServerAccount\Entity\Account $account
	 * @param string $platform
	 * @param array $credentials
	 */
	public function linkAccount(\DragonJsonServerAccount\Entity\Account $account, $platform, array $credentials)
	{
		$credentials = $this->getCredentials($platform, $credentials);
		$entityManager = $this->getEntityManager();

		try {
			$device = $this->getDeviceByPlatformAndCredentials($platform, $credentials);
		} catch (\Exception $exception) {
		}
		if (isset($device)) {
			throw new \DragonJsonServer\Exception('device already linked');
		}
		$device = (new \DragonJsonServerDevice\Entity\Device())
			->setAccountId($account->getAccountId())
			->setPlatform($platform)
			->setCredentials(\Zend\Json\Encoder::encode($credentials));
		$entityManager->persist($device);
		$entityManager->flush();
		return $device;
	}
	
    /**
	 * Entfernt die Deviceverknüpfung für den Account
	 * @param \DragonJsonServerDevice\Entity\Device $device
	 */
	public function unlinkAccount(\DragonJsonServerDevice\Entity\Device $device)
	{
		$entityManager = $this->getEntityManager();
		
		$entityManager->remove($device);
		$entityManager->flush();
	}
	
	/**
	 * Gibt das Device der übergebenen DeviceID zurück
	 * @param integer $device_id
	 * @return \DragonJsonServerDevice\Entity\Device
     * @throws \DragonJsonServer\Exception
	 */
	public function getDeviceById($device_id)
	{
		$entityManager = $this->getEntityManager();
		
		$device = $entityManager->find('\DragonJsonServerDevice\Entity\Device', $device_id);
		if (null === $device) {
			throw new \DragonJsonServer\Exception('incorrect device_id', ['device_id' => $device_id]);
		}
		return $device;
	}
	
	/**
	 * Gibt das Device der übergebenen Deviceverknüpfung zurück
	 * @param string $platform
	 * @param array $credentials
	 * @return \DragonJsonServerDevice\Entity\Device
	 */
	public function getDeviceByPlatformAndCredentials($platform, array $credentials)
	{
		$credentials = $this->getCredentials($platform, $credentials);
		$entityManager = $this->getEntityManager();

		$device = $entityManager
			->getRepository('\DragonJsonServerDevice\Entity\Device')
			->findOneBy(['credentials' => \Zend\Json\Encoder::encode($credentials)]);
		if (null === $device) {
			throw new \DragonJsonServer\Exception('incorrect credentials');
		}
		return $device;
	}
	
	/**
	 * Gibt die Credentials anhand der Plattform zurück
	 * @param string $platform
	 * @param array $credentials
	 * @throws \DragonJsonServer\Exception
	 */
	protected function getCredentials($platform, array $credentials)
	{
		$deviceplatforms = $this->getServiceManager()->get('Config')['deviceplatforms'];
		if (!isset($deviceplatforms[$platform])) {
			throw new \DragonJsonServer\Exception(
				'incorrect platform', 
				['platform' => $platform, 'deviceplatforms' => array_keys($deviceplatforms)]
			);
		}
		$normalizedCredentials = array();
		foreach ($deviceplatforms[$platform] as $key) {
			if (!isset($credentials[$key])) {
				throw new \DragonJsonServer\Exception('missing credential', ['key' => $key]);
			}
			$normalizedCredentials[$key] = $credentials[$key];
		}
		return $normalizedCredentials;
	}
}
