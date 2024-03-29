<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesReclamationGui\Communication\ReclamationItem;

use ArrayObject;

class ReclamationItemEventsFinder implements ReclamationItemEventsFinderInterface
{
    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ReclamationItemTransfer> $reclamationItems
     * @param array<array<string>> $eventsGroupedByItem
     *
     * @return array<string>
     */
    public function getDistinctManualEventsByReclamationItems(
        ArrayObject $reclamationItems,
        array $eventsGroupedByItem
    ): array {
        $orderItemsIds = $this->getOrderItemsIdsByReclamationItems($reclamationItems);
        $events = [];
        foreach ($orderItemsIds as $orderItemId) {
            if (isset($eventsGroupedByItem[$orderItemId])) {
                $events = array_merge($events, $eventsGroupedByItem[$orderItemId]);
            }
        }

        return array_unique($events);
    }

    /**
     * @param \ArrayObject<int, \Generated\Shared\Transfer\ReclamationItemTransfer> $reclamationItems
     *
     * @return array<int>
     */
    protected function getOrderItemsIdsByReclamationItems(ArrayObject $reclamationItems): array
    {
        $orderItemsIds = [];
        foreach ($reclamationItems as $item) {
            $orderItemsIds[] = $item->getOrderItem()->getIdSalesOrderItem();
        }

        return $orderItemsIds;
    }
}
