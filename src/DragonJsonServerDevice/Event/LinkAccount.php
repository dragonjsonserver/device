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
 * Eventklasse für die Verknüpfung eines Accounts mit einem Device
 */
class LinkAccount extends \Zend\EventManager\Event
{
	use \DragonJsonServer\ServiceManagerTrait { 
		getServiceManager as public; 
	}
	
	/**
	 * @var string
	 */
	protected $name = 'linkaccount';

    /**
     * Setzt den Account der mit dem Device verknüpft wurde
     * @param \DragonJsonServerAccount\Entity\Account $account
     * @return LinkAccount
     */
    public function setAccount(\DragonJsonServerAccount\Entity\Account $account)
    {
        $this->setParam('account', $account);
        return $this;
    }

    /**
     * Gibt den Account der mit dem Device verknüpft wurde zurück
     * @return \DragonJsonServerAccount\Entity\Account
     */
    public function getAccount()
    {
        return $this->getParam('account');
    }

    /**
     * Setzt das Device das mit dem Account verknüpft wurde
     * @param \DragonJsonServerDevice\Entity\Device $device
     * @return LinkAccount
     */
    public function setDevice(\DragonJsonServerDevice\Entity\Device $device)
    {
        $this->setParam('device', $device);
        return $this;
    }

    /**
     * Gibt das Device das mit dem Account verknüpft wurde zurück
     * @return \DragonJsonServerDevice\Entity\Device
     */
    public function getDevice()
    {
        return $this->getParam('device');
    }
}
