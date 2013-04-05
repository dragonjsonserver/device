<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2013 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServerDevice
 */

namespace DragonJsonServerDevice\Event;

/**
 * Eventklasse für den Login eines Accounts mit einem Device
 */
class Login extends \Zend\EventManager\Event
{
	use \DragonJsonServer\ServiceManagerTrait { 
		getServiceManager as public; 
	}
	
	/**
	 * @var string
	 */
	protected $name = 'login';

    /**
     * Setzt das Device das mit dem Account eingeloggt wurde
     * @param \DragonJsonServerDevice\Entity\Device $device
     * @return Login
     */
    public function setDevice(\DragonJsonServerDevice\Entity\Device $device)
    {
        $this->setParam('device', $device);
        return $this;
    }

    /**
     * Gibt das Device das mit dem Account eingeloggt wurde zurück
     * @return \DragonJsonServerDevice\Entity\Device
     */
    public function getDevice()
    {
        return $this->getParam('device');
    }
}
