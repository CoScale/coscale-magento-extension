<?php
/**
 * CoScale Event interaction model
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-03
 * @version 1.0
 *
 * @method setId(int $eventId)
 * @method setName(string $name)
 * @method setDescription(string $description)
 * @method setType(string $type)
 * @method setSource(string $source)
 * @method setVersion(int $version)
 * @method setTimestamp(int $timestamp)
 * @method setUpdatedAt(string $date)
 * @method int getId()
 * @method string getName()
 * @method string getDescription()
 * @method string getType()
 * @method string getSource()
 * @method int getState()
 * @method int getVersion()
 * @method int getTimestamp()
 * @method string getUpdatedAt()
 */ 
class CoScale_Monitor_Model_Event extends Mage_Core_Model_Abstract
{
	/**
	 * Disabled state for events
	 */
	const STATE_DISABLED = -1;

	/**
	 * Inactive state for events
	 */
	const STATE_INACTIVE = 0;

	/**
	 * Enabled/active state for events
	 */
	const STATE_ENABLED = 1;

	/**
	 * Construct the event model
	 */
    protected function _construct()
    {
        $this->_init('coscale_monitor/data');
    }

	/**
	 * Set the state for an event
	 *
	 * @param int $state
	 *
	 * @return $this
	 * @throws Exception
	 */
	public function setState($state)
	{
		if ( ! in_array($state, array(self::STATE_DISABLED, self::STATE_ENABLED, self::STATE_INACTIVE))) {
			throw new Exception('State must be one of the predefined state levels.');
		}

		return $this->setData('state', $state);
	}
}