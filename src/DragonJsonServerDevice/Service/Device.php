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
	use \DragonJsonServer\EventManagerTrait;
	use \DragonJsonServerDoctrine\EntityManagerTrait;
	
    /**
	 * Erstellt eine neue Deviceverknüpfung für den Account
	 * @param integer $account_id
	 * @param string $platform
	 * @param array $credentials
	 * @return \DragonJsonServerDevice\Entity\Device
	 */
	public function createDevice($account_id, $platform, array $credentials)
	{
		$credentials = $this->getCredentials($platform, $credentials);
		if (null !== $this->getDeviceByPlatformAndCredentials($platform, $credentials, false)) { 
			throw new \DragonJsonServer\Exception('device already linked', ['device' => $entity->toArray()]);
		}
		$device = (new \DragonJsonServerDevice\Entity\Device())
			->setAccountId($account_id)
			->setPlatform($platform)
			->setCredentials(\Zend\Json\Encoder::encode($credentials));
		$this->getServiceManager()->get('Doctrine')->transactional(function ($entityManager) use ($device) {
			$entityManager->persist($device);
			$entityManager->flush();
			$this->getEventManager()->trigger(
				(new \DragonJsonServerDevice\Event\CreateDevice())
					->setTarget($this)
					->setDevice($device)
			);
		});
		return $device;
	}
	
    /**
	 * Entfernt die Deviceverknüpfung für den Account
	 * @param \DragonJsonServerDevice\Entity\Device $device
	 * @return Device
	 */
	public function removeDevice(\DragonJsonServerDevice\Entity\Device $device)
	{
		$this->getServiceManager()->get('Doctrine')->transactional(function ($entityManager) use ($device) {
			$this->getEventManager()->trigger(
				(new \DragonJsonServerDevice\Event\RemoveDevice())
					->setTarget($this)
					->setDevice($device)
			);
			$entityManager->remove($device);
			$entityManager->flush();
		});
		return $this;
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
			throw new \DragonJsonServer\Exception('invalid device_id', ['device_id' => $device_id]);
		}
		return $device;
	}
	
	/**
	 * Gibt das Device der übergebenen AccountID zurück
	 * @param integer $account_id
	 * @param boolean $throwException
	 * @return \DragonJsonServerDevice\Entity\Device|null
     * @throws \DragonJsonServer\Exception
	 */
	public function getDeviceByAccountId($account_id, $throwException = true)
	{
		$entityManager = $this->getEntityManager();

		$device = $entityManager
			->getRepository('\DragonJsonServerDevice\Entity\Device')
			->findOneBy(['account_id' => $account_id]);
		if (null === $device && $throwException) {
			throw new \DragonJsonServer\Exception('invalid account_id', ['account_id' => $account_id]);
		}
		return $device;
	}
	
	/**
	 * Gibt das Device der übergebenen Deviceverknüpfung zurück
	 * @param string $platform
	 * @param array $credentials
	 * @param boolean $throwException
	 * @return \DragonJsonServerDevice\Entity\Device|null
     * @throws \DragonJsonServer\Exception
	 */
	public function getDeviceByPlatformAndCredentials($platform, 
													  array $credentials, 
													  $throwException = true)
	{
		$credentials = $this->getCredentials($platform, $credentials);
		$entityManager = $this->getEntityManager();

		$device = $entityManager
			->getRepository('\DragonJsonServerDevice\Entity\Device')
			->findOneBy(['platform' => $platform, 'credentials' => \Zend\Json\Encoder::encode($credentials)]);
		if (null === $device && $throwException) {
			throw new \DragonJsonServer\Exception(
				'invalid credentials', 
				['platform' => $platform, 'credentials' => $credentials]
			);
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
		$deviceplatforms = $this->getServiceManager()->get('Config')['dragonjsonserverdevice']['deviceplatforms'];
		if (!isset($deviceplatforms[$platform])) {
			throw new \DragonJsonServer\Exception(
				'invalid platform', 
				['platform' => $platform, 'deviceplatforms' => $deviceplatforms]
			);
		}
		$normalizedCredentials = [];
		foreach ($deviceplatforms[$platform] as $key) {
			if (!isset($credentials[$key])) {
				throw new \DragonJsonServer\Exception('missing key', ['key' => $key]);
			}
			$normalizedCredentials[$key] = $credentials[$key];
		}
		return $normalizedCredentials;
	}
}
