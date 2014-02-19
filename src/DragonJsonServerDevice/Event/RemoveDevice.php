<?php
/**
 * @link http://dragonjsonserver.de/
 * @copyright Copyright (c) 2012-2014 DragonProjects (http://dragonprojects.de/)
 * @license http://license.dragonprojects.de/dragonjsonserver.txt New BSD License
 * @author Christoph Herrmann <developer@dragonprojects.de>
 * @package DragonJsonServerDevice
 */

namespace DragonJsonServerDevice\Event;

/**
 * Eventklasse für die Trennung eines Accounts mit einem Device
 */
class RemoveDevice extends \Zend\EventManager\Event
{
	/**
	 * @var string
	 */
	protected $name = 'RemoveDevice';

    /**
     * Setzt das Device das mit dem Account getrennt wurde
     * @param \DragonJsonServerDevice\Entity\Device $device
     * @return RemoveDevice
     */
    public function setDevice(\DragonJsonServerDevice\Entity\Device $device)
    {
        $this->setParam('device', $device);
        return $this;
    }

    /**
     * Gibt das Device das mit dem Account getrennt wurde zurück
     * @return \DragonJsonServerDevice\Entity\Device
     */
    public function getDevice()
    {
        return $this->getParam('device');
    }
}
