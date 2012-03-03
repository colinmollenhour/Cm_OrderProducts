<?php
/**
 * Adminhtml grid skus renderer
 *
 * @author Colin Mollenhour <colin@mollenhour.com> http://colin.mollenhour.com
 */
class Cm_OrderProducts_Block_Adminhtml_Sales_Order_Grid_Renderer_Skus extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        $skus = explode('^^', $row->getSkus());
        $qtys = explode('^^', $row->getQtys());
        $names = explode('^^', $row->getNames());
        $html = '';
        foreach ($skus as $i => $sku) {
            if($qtys[$i] == round($qtys[$i],0)) {
              $html .= sprintf('<tr title="%s" style="cursor:default;"><td>%s</td><td style="width:1em;">%d</td></tr>', $names[$i], $sku, $qtys[$i]);
            } else {
              $html .= sprintf('<tr title="%s" style="cursor:default;"><td>%s</td><td style="width:1em;">%.4f</td></tr>', $names[$i], $sku, $qtys[$i]);
            }
        }
        return '<table style="border: 0; border-collapse: collapse;"><tbody>'.$html.'</tbody></table>';
    }

}
