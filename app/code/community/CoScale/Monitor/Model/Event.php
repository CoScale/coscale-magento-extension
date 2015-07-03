<?php
/**
 * CoScale Event interaction model
 *
 * @package CoScale_Monitor
 * @author Rian Orie <rian.orie@supportdesk.nu>
 * @created 2015-07-03
 * @version 1.0
 *
 * @method $this setId(int $eventId)
 * @method $this setName(string $name)
 * @method $this setDescription(string $description)
 * @method $this setSource(string $source)
 * @method $this setVersion(int $version)
 * @method $this setTimestamp(int $timestamp)
 * @method $this setUpdatedAt(string $date)
 * @method int getId()
 * @method string getName()
 * @method string getDescription()
 * @method string getType()
 * @method string getSource()
 * @method int getState()
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
	 * Event type for adding stores
	 */
	const TYPE_STORE_ADD = 'STORE_ADD';

	/**
	 * Construct the event model
	 */
    protected function _construct()
    {
        $this->_init('coscale_monitor/event');
    }

	/**
	 * Make sure the version is always an integer, even when it's null
	 *
	 * @return int
	 */
	public function getVersion()
	{
		if (is_null($this->getData('version'))) {
			return 0;
		}

		return $this->getData('version');
	}

	/**
	 * Return the event data as an array, rather than the json string
	 *
	 * @return array
	 */
	public function getEventData()
	{
		return json_decode($this->getData('event_data'), true);
	}

	/**
	 * Set the state for an event
	 *
	 * @param int $state Set the state to one of the predefined states in the system
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

	/**
	 * Set the event type
	 *
	 * @param string $type Set the type of the event to one of the predefined types
	 *
	 * @return $this
	 *
	 * @throws Exception
	 */
	public function setType($type)
	{
		if ( ! in_array($type, array(self::TYPE_STORE_ADD))) {
			throw new Exception('Only predefined types can be used.');
		}

		return $this->setData('type', $type);
	}

	/**
	 * Store the event data as a json string rather than a raw array
	 *
	 * @param array $data An array of data to expose to CoScale
	 *
	 * @return $this
	 */
	public function setEventData(array $data)
	{
		return $this->setData('event_data', json_encode($data));
	}

	/**
	 * Shorthand for adding new events
	 *
	 * @param int    $type        The event type as predefined in this model
	 * @param string $name        Short name for the event
	 * @param string $description Description of the event
	 * @param array  $data        An array of data to expose to CoScale
	 * @param string $source      The causer of the event, logged in user, etc
	 *
	 * @return $this
	 */
	public function addEvent($type, $name, $description, array $data, $source)
	{
		$this->setType($type)
		     ->setName($name)
		     ->setDescription($description)
		     ->setEventData($data)
		     ->setSource($source);

		$this->save();

		return $this;
	}

	/**
	 * Set some defaults before saving an event to the database
	 *
	 * @return $this
	 */
	protected function _beforeSave()
	{
		$date = Mage::getModel('core/date');

		$this->setTimestamp($date->timestamp())
			 ->setUpdatedAt($date->date())
			 ->setVersion($this->getVersion()+1);

		return $this;
	}
}