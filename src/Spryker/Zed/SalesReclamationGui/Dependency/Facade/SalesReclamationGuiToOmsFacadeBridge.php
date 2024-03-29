<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesReclamationGui\Dependency\Facade;

class SalesReclamationGuiToOmsFacadeBridge implements SalesReclamationGuiToOmsFacadeInterface
{
    /**
     * @var \Spryker\Zed\Oms\Business\OmsFacadeInterface
     */
    protected $omsFacade;

    /**
     * @param \Spryker\Zed\Oms\Business\OmsFacadeInterface $omsFacade
     */
    public function __construct($omsFacade)
    {
        $this->omsFacade = $omsFacade;
    }

    /**
     * @param int $idSalesOrder
     *
     * @return array<array<string>>
     */
    public function getManualEventsByIdSalesOrder($idSalesOrder): array
    {
        return $this->omsFacade->getManualEventsByIdSalesOrder($idSalesOrder);
    }
}
