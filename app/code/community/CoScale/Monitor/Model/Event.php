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
 * @method $this setDuration(int $duration)
 * @method $this setTimestampStart(int $timestamp)
 * @method $this setTimestampEnd(int $timestamp)
 * @method $this setUpdatedAt(string $date)
 * @method int getId()
 * @method string getName()
 * @method string getDescription()
 * @method string getType()
 * @method string getSource()
 * @method int getState()
 * @method int getTimestampStart()
 * @method int getTimestampEnd()
 * @method int getDuration()
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

    const GROUP_ADMIN = 'Admin actions';
    const GROUP_CRON = 'Cron Jobs';

    /**
     * Event type for adding stores
     */
    const TYPE_STORE_ADD = 'STORE_ADD';

    /**
     * Event type for flushing the page cache
     */
    const TYPE_FLUSH_SYSTEM_CACHE = 'FLUSH_SYSTEM_CACHE';
    const TYPE_FLUSH_ALL_CACHE = 'FLUSH_ALL_CACHE';
    const TYPE_MASS_REFRESH_CACHE = 'MASS_REFRESH_CACHE';

    /**
     * Event for flushing the asset cache
     */
    const TYPE_FLUSH_ASSET_CACHE = 'FLUSH_ASSET_CACHE';

    /**
     * Event for flushing the image cache
     */
    const TYPE_FLUSH_IMAGE_CACHE = 'FLUSH_IMAGE_CACHE';

    /**
     * Event for the reindexing
     */
    const TYPE_REINDEX = 'REINDEX';

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
        if (!in_array($state, array(self::STATE_DISABLED, self::STATE_ENABLED, self::STATE_INACTIVE))) {
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
        if (!in_array($type, $this->getTypes())) {
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
     * @param string $type The event type as predefined in this model
     * @param string $name Short name for the event
     * @param string $description Description of the event
     * @param array $data An array of data to expose to CoScale
     * @param string $source The causer of the event, logged in user, etc
     * @param int $state The state of the event
     *
     * @return $this
     */
    public function addEvent($type, $name, $description, array $data, $source, $state = null)
    {
        $this->setType($type)
            ->setName($name)
            ->setDescription($description)
            ->setEventData($data) 
            ->setDuration(0)
            ->setTimestampStart(time())
            ->setSource($source)
            ->setState($state);

        // By default we're assuming events don't do much and we're simply logging them
        // if an event takes longer, the enabled state needs to be defined in the call
        if (is_null($state)) {
            $this->setState(self::STATE_INACTIVE)
                ->setTimestampEnd(time());
        }

        try {
            $this->save();
        } catch (Exception $ex) {
            Mage::log($ex->getMessage(), null, 'coscale.log', true);
        }

        return $this;
    }

    /**
     * Shorthand to update an event
     *
     * @param string $type The predefined type to load the event by
     * @param int $state The state of the event
     * @param string $source Source of the event, who triggered it
     * @param array $data Event data to store
     *
     * @throws Exception
     */
    public function updateEvent($type, $state, $source = null, array $data = null)
    {
        $this->loadLastByType($type);
        $this->setTimestampEnd(time())
            ->setState($state);

        if (!is_null($source)) {
            $this->setSource($source);
        }

        if (!is_null($data)) {
            $this->setEventData($data);
        }

        try {
            $this->save();
        } catch (Exception $ex) {
            Mage::log($ex->getMessage(), null, 'coscale.log', true);
        }
    }

    /**
     * Load an event by it's type
     *
     * @param string $type The predefined type to load the event by
     *
     * @return $this
     *
     * @throws Exception
     */
    public function loadLastByType($type)
    {
        if (!in_array($type, $this->getTypes())) {
            throw new Exception('Type should be one of the predefined keys');
        }

        $this->_getResource()->loadByType($this, $type);
        return $this;
    }

    /**
     * Type groups for the reporting and grouping of types
     *
     * @return string
     */
    public function getTypeGroup()
    {
        switch ($this->getType()) {
            case self::TYPE_STORE_ADD:
            case self::TYPE_MASS_REFRESH_CACHE:
            case self::TYPE_FLUSH_ASSET_CACHE:
            case self::TYPE_FLUSH_IMAGE_CACHE:
            case self::TYPE_FLUSH_SYSTEM_CACHE:
            case self::TYPE_FLUSH_ALL_CACHE:
            case self::TYPE_REINDEX:
                return self::GROUP_ADMIN;
        }
    }

    /**
     * Set some defaults before saving an event to the database
     *
     * @return $this
     */
    protected function _beforeSave()
    {
        $date = Mage::getModel('core/date');

        $this->setUpdatedAt($date->date())
            ->setVersion($this->getVersion() + 1);

        return $this;
    }

    /**
     * Retrieve the available types as an array
     *
     * @return array
     */
    protected function getTypes()
    {
        return array(self::TYPE_REINDEX, self::TYPE_STORE_ADD,
            self::TYPE_FLUSH_ASSET_CACHE, self::TYPE_FLUSH_IMAGE_CACHE,
            self::TYPE_MASS_REFRESH_CACHE, self::TYPE_FLUSH_SYSTEM_CACHE,
            self::TYPE_FLUSH_ALL_CACHE);
    }
}
