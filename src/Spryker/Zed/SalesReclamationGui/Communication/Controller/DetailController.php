<?php

/**
 * Copyright © 2016-present Spryker Systems GmbH. All rights reserved.
 * Use of this software requires acceptance of the Evaluation License Agreement. See LICENSE file.
 */

namespace Spryker\Zed\SalesReclamationGui\Communication\Controller;

use Generated\Shared\Transfer\ReclamationTransfer;
use Spryker\Service\UtilText\Model\Url\Url;
use Spryker\Zed\Kernel\Communication\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @method \Spryker\Zed\SalesReclamationGui\Communication\SalesReclamationGuiCommunicationFactory getFactory()
 */
class DetailController extends AbstractController
{
    /**
     * @var string
     */
    protected const PARAM_ID_RECLAMATION_ITEM = 'id-reclamation-item';

    /**
     * @var string
     */
    protected const PARAM_ID_RECLAMATION = 'id-reclamation';

    /**
     * @var string
     */
    protected const ROUTE_REDIRECT = '/sales-reclamation-gui/detail';

    /**
     * @var string
     */
    protected const ROUTE_RECLAMATION_LIST = '/sales-reclamation-gui';

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|array
     */
    public function indexAction(Request $request)
    {
        $idReclamation = $this->castId($request->get(static::PARAM_ID_RECLAMATION));
        $reclamationTransfer = new ReclamationTransfer();
        $reclamationTransfer->setIdSalesReclamation($idReclamation);

        $reclamationTransfer = $this->getFactory()
            ->getSalesReclamationFacade()
            ->getReclamationById($reclamationTransfer);

        $reclamationTransfer->requireOrder();

        $eventsGroupedByItem = $this->getFactory()
            ->getOmsFacade()
            ->getManualEventsByIdSalesOrder($reclamationTransfer->getOrder()->getIdSalesOrder());

        $events = $this->getFactory()
            ->createReclamationItemEventsFinder()
            ->getDistinctManualEventsByReclamationItems($reclamationTransfer->getReclamationItems(), $eventsGroupedByItem);

        $changeStatusRedirectUrl = $this->createRedirectLink($idReclamation);

        return $this->viewResponse([
            'reclamation' => $reclamationTransfer,
            'eventsGroupedByItem' => $eventsGroupedByItem,
            'events' => $events,
            'changeStatusRedirectUrl' => $changeStatusRedirectUrl,
            'idSalesOrder' => $reclamationTransfer->getOrder()->getIdSalesOrder(),
        ]);
    }

    /**
     * @param int $idReclamation
     *
     * @return string
     */
    protected function createRedirectLink(int $idReclamation): string
    {
        $redirectUrlParams = [
            static::PARAM_ID_RECLAMATION => $idReclamation,
        ];

        return Url::generate(static::ROUTE_REDIRECT, $redirectUrlParams);
    }

    /**
     * @param \Symfony\Component\HttpFoundation\Request $request
     *
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function closeAction(Request $request): RedirectResponse
    {
        $form = $this->getFactory()->getCloseReclamationForm()->handleRequest($request);

        if (!$form->isSubmitted() || !$form->isValid()) {
            $this->addErrorMessage('CSRF token is not valid.');

            return $this->redirectResponse(static::ROUTE_RECLAMATION_LIST);
        }

        $idReclamation = $this->castId($request->get(static::PARAM_ID_RECLAMATION));

        $reclamationTransfer = new ReclamationTransfer();
        $reclamationTransfer->setIdSalesReclamation($idReclamation);

        $reclamationTransfer = $this->getFactory()
            ->getSalesReclamationFacade()
            ->closeReclamation($reclamationTransfer);

        $this->addSuccessMessage('Reclamation with id %id% closed', [
            '%id%' => $reclamationTransfer->getIdSalesReclamation(),
        ]);

        return $this->redirectResponse(Url::generate(static::ROUTE_RECLAMATION_LIST)->build());
    }
}
