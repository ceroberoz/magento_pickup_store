<?php

class Magento_Pickup_Model_Carrier
    extends Mage_Shipping_Model_Carrier_Abstract
    implements Mage_Shipping_Model_Carrier_Interface
{
    /**
     * Carrier's code, as defined in parent class
     *
     * @var string
     */
    protected $_code = 'magento_pickup';

    /**
     * Returns available shipping rates for Inchoo Shipping carrier
     *
     * @param Mage_Shipping_Model_Rate_Request $request
     * @return Mage_Shipping_Model_Rate_Result
     */
    public function collectRates(Mage_Shipping_Model_Rate_Request $request)
    {
        /** @var Mage_Shipping_Model_Rate_Result $result */
        $result = Mage::getModel('shipping/rate_result');

        /** @var Inchoo_Shipping_Helper_Data $expressMaxProducts */
        $expressMaxWeight = Mage::helper('magento_pickup')->getExpressMaxWeight();

        $expressAvailable = false;
        foreach ($request->getAllItems() as $item) {
            if ($item->getWeight() > $expressMaxWeight) {
                $expressAvailable = false;
            }
        }

        if ($expressAvailable) {
            $result->append($this->_getExpressRate());
        }
        $result->append($this->_getStandardRate());

        return $result;
    }

    /**
     * Returns Allowed shipping methods
     *
     * @return array
     */
    public function getAllowedMethods()
    {
        return array(
            'regular'    =>  'Magento Pickup',
            'express'     =>  'Magento Pickup Express',
        );
    }

    /**
     * Get Standard rate object
     *
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    protected function _getStandardRate()
    {
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('regular');
        $rate->setMethodTitle('Pick up at store (Plaza Indonesia Level 4)'); // set name here
        $rate->setPrice(0); // set shiping cost here
        $rate->setCost(0);

        return $rate;
    }

    /**
     * Get Express rate object
     *
     * @return Mage_Shipping_Model_Rate_Result_Method
     */
    protected function _getExpressRate()
    {
        /** @var Mage_Shipping_Model_Rate_Result_Method $rate */
        $rate = Mage::getModel('shipping/rate_result_method');

        $rate->setCarrier($this->_code);
        $rate->setCarrierTitle($this->getConfigData('title'));
        $rate->setMethod('express');
        $rate->setMethodTitle('Magento Pickup Express');
        $rate->setPrice(12.3);
        $rate->setCost(0);

        return $rate;
    }
}
