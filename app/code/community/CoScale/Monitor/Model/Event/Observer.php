<?php

class CoScale_Monitor_Model_Event_Observer
{
    public function generate(Varien_Event_Observer $observer)
    {
        $event = $observer->getEvent();

        /** @var CoScale_Monitor_Helper_Data $logger */
        $logger = $event->getLogger();
        /** @var CoScale_Monitor_Model_Output_Generator $output */
        $output = $event->getOutput();

        // Get Events Collection
        try {
            $logger->debugStart('Events Collection');

            $collection = Mage::getModel('coscale_monitor/event')->getCollection();
            /** @var CoScale_Monitor_Model_Event $event */
            foreach ($collection as $event) {
                $output->addEvent(
                    array(
                        'type' => $event->getTypeGroup(),
                        'message' => $event->getDescription(),
                        'data' => array_merge(
                            array('originator' => $event->getSource()),
                            unserialize($event->getEventData())
                        ),
                        'start_time' => (int)(time() - $event->getTimestampStart()),
                        'stop_time' => (int)($event->getTimestampEnd() != 0 ? (time() - $event->getTimestampEnd()) : 0),
                    )
                );

                $event->delete();
                if ($event->getState() != $event::STATE_ENABLED) {
                    //$event->delete();
                }
            }
            $logger->debugEnd('Events Collection');
        } catch (Exception $ex) {
            $logger->debugEndError('Events Collection', $ex);
        }

        // Get Cronjob Collection
        try {
            $logger->debugStart('Cronjob Collection');

            $endDateTime = date('U');
            $collection = Mage::getModel('cron/schedule')->getCollection()
                ->addFieldToFilter('finished_at', array('from' => date('Y-m-d H:i:s', $endDateTime-65)))
                ->setOrder('finished_at', 'DESC');
            /** @var Mage_Cron_Model_Schedule $event */
            foreach ($collection as $cron) {
                $output['events'][] = array(
                    'type' => CoScale_Monitor_Model_Event::GROUP_CRON,
                    'message' => $cron->getJobCode(),
                    'status' => $cron->getStatus(),
                    'start_time' => (int)(time() - strtotime($cron->getExecutedAt())),
                    'stop_time' => (int)(time() - strtotime($cron->getFinishedAt())),
                );
            }
            $logger->debugEnd('Cronjob Collection');
        } catch (Exception $ex) {
            $logger->debugEndError('Events Collection', $ex);
        }

        try {
            $logger->debugStart('Maintenance Flag');
            if (file_exists(Mage::getBaseDir('base') . DS . 'maintenance.flag')) {
                $output['events'][] = array(
                    'type' => CoScale_Monitor_Model_Event::GROUP_ADMIN,
                    'message' => 'Maintenance mode enabled',
                    'start_time' => 0,
                    'stop_time' => 0,
                );
            }
            $logger->debugEnd('Maintenance Flag');
        } catch (Exception $ex) {
            $logger->debugEndError('Maintenance Flag', $ex);
        }

    }
}