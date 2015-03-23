<?php
/**
 * Adminhtml grid skus renderer
 *
 * @author Colin Mollenhour <colin@mollenhour.com> http://colin.mollenhour.com
 */
class Cm_OrderProducts_Block_Adminhtml_Sales_Order_Grid_Renderer_Products extends Mage_Adminhtml_Block_Widget_Grid_Column_Renderer_Abstract
{

    /**
     * Renders grid column
     *
     * @param   Varien_Object $row
     * @return  string
     */
    public function render(Varien_Object $row)
    {
        if ( ! $row->getSkus()) {
            return '';
        }
        $skus = explode('^', $row->getSkus());
        $qtys = explode('^', $row->getQtys());
        $names = explode('^', $row->getNames());
        $html = '';
        if (count($skus) != count($names) || count($skus) != count($qtys)) {
            return '<span style="color: red;">Error, missing product SKUs or names.</span>';
        }
        switch($this->getColumn()->getRenderColumn()) {
            case 'skus':
                foreach ($skus as $i => $sku) {
                    if($qtys[$i] == round($qtys[$i],0)) {
                      $html .= sprintf('<tr title="%s" style="cursor:default;"><td>%s</td><td style="width:1em;">%d</td></tr>', trim($names[$i]), $sku, trim($qtys[$i]));
                    } else {
                      $html .= sprintf('<tr title="%s" style="cursor:default;"><td>%s</td><td style="width:1em;">%.4f</td></tr>', trim($names[$i]), $sku, trim($qtys[$i]));
                    }
                }
                break;
            case 'names':
                foreach ($skus as $i => $sku) {
                    if($qtys[$i] == round($qtys[$i],0)) {
                      $html .= sprintf('<tr title="%s" style="cursor:default;"><td>%s</td><td style="width:1em;">%d</td></tr>', $sku, trim($names[$i]), trim($qtys[$i]));
                    } else {
                      $html .= sprintf('<tr title="%s" style="cursor:default;"><td>%s</td><td style="width:1em;">%.4f</td></tr>', $sku, trim($names[$i]), trim($qtys[$i]));
                    }
                }
                break;
        }
        return '<table style="border: 0; border-collapse: collapse;"><tbody>'.$html.'</tbody></table>';
    }

}
