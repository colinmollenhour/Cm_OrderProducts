<?php
/**
 * Add Product SKUs column to orders grid
 *
 * @author Colin Mollenhour <colin@mollenhour.com> http://colin.mollenhour.com
 */
class Cm_OrderProducts_Block_Adminhtml_Sales_Order_Grid extends Mage_Adminhtml_Block_Sales_Order_Grid
{

    const XML_PATH_RENDER_COLUMN = 'sales/cmorderproducts/render';
    const XML_PATH_FILTER_COLUMN = 'sales/cmorderproducts/filter';

    /**
     * MUST override setCollection rather than _prepareCollection to get filtering and paging both working
     *
     * @param Mage_Sales_Model_Mysql4_Order_Grid_Collection $collection
     * @return
     */
    public function setCollection($collection)
    {
        parent::setCollection($collection);
        if ( ! Mage::getStoreConfig(self::XML_PATH_RENDER_COLUMN) || $this->_isExport) return;

        $collection->getSize(); // Get size before adding join
        $collection->join(
            'sales/order_item',
            '`sales/order_item`.order_id=`main_table`.entity_id',
            array(
                'skus'  => new Zend_Db_Expr('group_concat(`sales/order_item`.sku SEPARATOR " ^ ")'),
                'qtys'  => new Zend_Db_Expr('group_concat(`sales/order_item`.qty_ordered SEPARATOR " ^ ")'),
                'names' => new Zend_Db_Expr('group_concat(`sales/order_item`.name SEPARATOR " ^ ")'),
            )
        );
        $collection->getSelect()->group('entity_id');
    }

    protected function _prepareColumns()
    {
        parent::_prepareColumns();
        if( ! Mage::getStoreConfig(self::XML_PATH_RENDER_COLUMN) || $this->_isExport) return;

        // Specify table to fix ambiguous column errors
        foreach($this->getColumns() as $column) {
            if($column->getIndex()) {
                $column->setFilterIndex('`main_table`.'.$column->getIndex());
            }
        }

        $this->addColumnAfter('skus', array(
            'header'    => Mage::helper('sales')->__('Products Ordered (%s)', Mage::getStoreConfig(self::XML_PATH_RENDER_COLUMN)),
            'index'     => 'skus',
            'type'      => 'text',
            'filter_index' => '`sales/order_item`.'. Mage::getStoreConfig(self::XML_PATH_FILTER_COLUMN),
            'sortable'  => FALSE,
            'renderer'  => 'Cm_OrderProducts_Block_Adminhtml_Sales_Order_Grid_Renderer_Products',
            'render_column' =>  Mage::getStoreConfig(self::XML_PATH_RENDER_COLUMN),
        ), 'shipping_name');

        $this->sortColumnsByOrder();
    }

}
