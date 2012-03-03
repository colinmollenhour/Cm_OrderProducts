# Description

This module adds a "Products Ordered" column to the Magento orders grid. It can be configured to
display either SKUs or Names and will display the quantities ordrered in both cases. The column
can be used to filter orders to only those that contain matching SKUs or names depending on configuration.

# Installation

1. If [modman](https://github.com/colinmollenhour/modman) is not yet installed, install it and run `modman init` in the Magento root.
2. Run `modman clone Cm_OrderProducts git://github.com/colinmollenhour/Cm_OrderProducts.git`

# Known Issues

* When filtering by sku/name, the pager shows the full count rather than the filtered count and only
the matching products are shown in the column.

# Author

[Colin Mollenhour](http://colin.mollenhour.com/)