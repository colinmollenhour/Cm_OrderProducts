<?php
/**
 * Choose which column to display in grid.
 *
 * @author Colin Mollenhour <colin@mollenhour.com> http://colin.mollenhour.com
 */
class Cm_OrderProducts_Model_System_Config_Source_Render
{
    /**
     * @return array
     */
    public function toOptionArray()
    {
        return array(
            array(
                'value' => '',
                'label' => Mage::helper('adminhtml')->__('None')
            ),
            array(
                'value' => 'skus',
                'label' => Mage::helper('adminhtml')->__('Product Skus')
            ),
            array(
                'value' => 'names',
                'label' => Mage::helper('adminhtml')->__('Product Names')
            ),
        );
    }
}
