<?php
/**
 * Choose which column to filter in grid.
 *
 * @author Colin Mollenhour <colin@mollenhour.com> http://colin.mollenhour.com
 */
class Cm_OrderProducts_Model_System_Config_Source_Filter
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => 'sku',
                'label' => Mage::helper('adminhtml')->__('Product Skus')
            ),
            array(
                'value' => 'name',
                'label' => Mage::helper('adminhtml')->__('Product Names')
            ),
        );
    }
}
