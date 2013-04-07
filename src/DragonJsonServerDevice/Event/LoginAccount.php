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
class LoginAccount extends \Zend\EventManager\Event
{
	/**
	 * @var string
	 */
	protected $name = 'loginaccount';

    /**
     * Setzt das Device das mit dem Account eingeloggt wurde
     * @param \DragonJsonServerDevice\Entity\Device $device
     * @return LoginAccount
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
