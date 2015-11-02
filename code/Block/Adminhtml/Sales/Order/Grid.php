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
     */
    public function setCollection($collection)
    {
        parent::setCollection($collection);
        if ( ! Mage::getStoreConfig(self::XML_PATH_RENDER_COLUMN) || $this->_isExport) return;

        // Only join if we have to
        $filters = $this->getParam($this->getVarNameFilter(), null);
        if (is_string($filters)) {
            $filters = $this->helper('adminhtml')->prepareFilterString($filters);
        }
        if ( $filters && (is_array($filters) && ! empty($filters['skus'])))
        {
            $collection->getSize(); // Get size before adding join
            $collection->join(
              ['soi' => 'sales/order_item'],
              'soi.order_id=main_table.entity_id',
              array()
            );
            $collection->getSelect()->group('entity_id');
        }
    }

    /**
     * Adding item data using second query because:
     *
     * - Join causes use of temporary table == slow
     * - Wrapping the main query as a subquery is too complex
     * - We want to show all order items for orders that matched filter by sku/name
     *
     * @return $this
     */
    protected function _prepareCollection()
    {
        parent::_prepareCollection();

        if (Mage::getStoreConfig(self::XML_PATH_RENDER_COLUMN) && ! $this->_isExport)
        {
            $orderIds = array();
            $orderCollection = $this->getCollection(); /** @var $orderCollection Mage_Sales_Model_Mysql4_Order_Grid_Collection */
            foreach ($orderCollection as $order) {
                $orderIds[] = $order->getEntityId();
            }
            $conn = Mage::getSingleton('core/resource')->getConnection('read'); /* @var $conn Zend_Db_Adapter_Pdo_Abstract */

            // Increase max length of group concat fields for long product names
            $conn->exec('SET SESSION group_concat_max_len = 4096;');

            $itemsCollection = new Varien_Data_Collection_Db($conn);
            $itemsCollection->getSelect()
                            ->from(array('soi' => $orderCollection->getTable('sales/order_item')), array(
                                'order_id',
                                'skus'  => new Zend_Db_Expr('group_concat(`soi`.sku SEPARATOR " ^ ")'),
                                'qtys'  => new Zend_Db_Expr('group_concat(`soi`.qty_ordered SEPARATOR " ^ ")'),
                                'names' => new Zend_Db_Expr('group_concat(`soi`.name SEPARATOR " ^ ")'),
                            ))
                            ->where('order_id IN (?)', $orderIds)
                            ->group('order_id');
            foreach ($itemsCollection as $object)
            {
                $order = $orderCollection->getItemById($object->getOrderId());
                $order->setSkus($object->getSkus());
                $order->setQtys($object->getQtys());
                $order->setNames($object->getNames());
            }

        }

        Mage::app()->dispatchEvent('cm_orderproducts_sales_order_grid_prepareCollection', ['block' => $this]);

        return $this;
    }

    /**
     * @return $this
     */
    protected function _prepareColumns()
    {
        parent::_prepareColumns();

        if (Mage::getStoreConfig(self::XML_PATH_RENDER_COLUMN) && ! $this->_isExport)
        {
            // Specify table to fix ambiguous column errors
            foreach($this->getColumns() as $column) {
                if($column->getIndex()) {
                    $column->setFilterIndex('main_table.'.$column->getIndex());
                }
            }

            $this->addColumnAfter('skus', array(
                'header'    => Mage::helper('sales')->__('Products Ordered (%s)', Mage::getStoreConfig(self::XML_PATH_RENDER_COLUMN)),
                'index'     => 'skus',
                'type'      => 'text',
                'filter_index' => 'soi.'. Mage::getStoreConfig(self::XML_PATH_FILTER_COLUMN),
                'sortable'  => FALSE,
                'renderer'  => 'Cm_OrderProducts_Block_Adminhtml_Sales_Order_Grid_Renderer_Products',
                'render_column' =>  Mage::getStoreConfig(self::XML_PATH_RENDER_COLUMN),
            ), 'shipping_name');

            $this->sortColumnsByOrder();
        }

        Mage::app()->dispatchEvent('cm_orderproducts_sales_order_grid_prepareColumns', ['block' => $this]);

        return $this;
    }

}
