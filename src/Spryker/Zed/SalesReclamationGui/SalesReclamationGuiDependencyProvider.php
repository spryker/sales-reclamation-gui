<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesReclamationGui;

use Orm\Zed\SalesReclamation\Persistence\SpySalesReclamationQuery;
use Spryker\Zed\Kernel\AbstractBundleDependencyProvider;
use Spryker\Zed\Kernel\Container;
use Spryker\Zed\SalesReclamationGui\Dependency\Facade\SalesReclamationGuiToOmsFacadeBridge;
use Spryker\Zed\SalesReclamationGui\Dependency\Facade\SalesReclamationGuiToSalesFacadeBridge;
use Spryker\Zed\SalesReclamationGui\Dependency\Facade\SalesReclamationGuiToSalesReclamationFacadeBridge;
use Spryker\Zed\SalesReclamationGui\Dependency\Service\SalesReclamationGuiToUtilDateTimeServiceBridge;

/**
 * @method \Spryker\Zed\SalesReclamationGui\SalesReclamationGuiConfig getConfig()
 */
class SalesReclamationGuiDependencyProvider extends AbstractBundleDependencyProvider
{
    /**
     * @var string
     */
    public const FACADE_SALES_RECLAMATION = 'FACADE_SALES_RECLAMATION';

    /**
     * @var string
     */
    public const FACADE_SALES = 'FACADE_SALES';

    /**
     * @var string
     */
    public const FACADE_OMS = 'FACADE_OMS';

    /**
     * @var string
     */
    public const PROPEL_QUERY_SALES_RECLAMATION = 'PROPEL_QUERY_SALES_RECLAMATION';

    /**
     * @var string
     */
    public const SERVICE_DATETIME = 'SERVICE_DATETIME';

    /**
     * @uses \Spryker\Zed\Form\Communication\Plugin\Application\FormApplicationPlugin::SERVICE_FORM_CSRF_PROVIDER
     *
     * @var string
     */
    public const SERVICE_FORM_CSRF_PROVIDER = 'form.csrf_provider';

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    public function provideCommunicationLayerDependencies(Container $container): Container
    {
        $container = parent::provideCommunicationLayerDependencies($container);

        $container = $this->addSalesFacade($container);
        $container = $this->addSalesReclamationFacade($container);
        $container = $this->addOmsFacade($container);
        $container = $this->addSalesReclamationPropelQuery($container);
        $container = $this->addDateTimeService($container);
        $container = $this->addCsrfProviderService($container);

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSalesReclamationFacade(Container $container): Container
    {
        $container->set(static::FACADE_SALES_RECLAMATION, function (Container $container) {
            return new SalesReclamationGuiToSalesReclamationFacadeBridge($container->getLocator()->salesReclamation()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addDateTimeService(Container $container): Container
    {
        $container->set(static::SERVICE_DATETIME, function (Container $container) {
            return new SalesReclamationGuiToUtilDateTimeServiceBridge(
                $container->getLocator()->utilDateTime()->service(),
            );
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSalesReclamationPropelQuery(Container $container): Container
    {
        $container->set(static::PROPEL_QUERY_SALES_RECLAMATION, $container->factory(function () {
            return SpySalesReclamationQuery::create();
        }));

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addSalesFacade(Container $container): Container
    {
        $container->set(static::FACADE_SALES, function (Container $container) {
            return new SalesReclamationGuiToSalesFacadeBridge($container->getLocator()->sales()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addOmsFacade(Container $container): Container
    {
        $container->set(static::FACADE_OMS, function (Container $container) {
            return new SalesReclamationGuiToOmsFacadeBridge($container->getLocator()->oms()->facade());
        });

        return $container;
    }

    /**
     * @param \Spryker\Zed\Kernel\Container $container
     *
     * @return \Spryker\Zed\Kernel\Container
     */
    protected function addCsrfProviderService(Container $container): Container
    {
        $container->set(static::SERVICE_FORM_CSRF_PROVIDER, function (Container $container) {
            return $container->getApplicationService(static::SERVICE_FORM_CSRF_PROVIDER);
        });

        return $container;
    }
}
