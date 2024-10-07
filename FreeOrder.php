<?php

/*
 * This file is part of the Thelia package.
 * http://www.thelia.net
 *
 * (c) OpenStudio <info@thelia.net>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace FreeOrder;

use Thelia\Core\Event\Order\OrderEvent;
use Thelia\Core\Event\TheliaEvents;
use Thelia\Core\Translation\Translator;
use Thelia\Exception\TheliaProcessException;
use Thelia\Model\Order;
use Thelia\Model\OrderStatusQuery;
use Thelia\Module\AbstractPaymentModule;

class FreeOrder extends AbstractPaymentModule
{
    public const MESSAGE_DOMAIN = "freeorder";

    /**
     * @return bool
     */
    public function isValidPayment()
    {
        return round($this->getCurrentOrderTotalAmount(), 2) === 0.0;
    }

    /**
     * @param Order $order
     * @return void
     * @throws \Propel\Runtime\Exception\PropelException
     */
    public function pay(Order $order): void
    {
        // Order total should be zero
        if ($order->getTotalAmount() > 0.0) {
            throw new TheliaProcessException(
                Translator::getInstance()?->trans(
                    "This payment type is not valid for this order. Please select another payment type.",
                    [],
                    self::MESSAGE_DOMAIN
                )
            );
        }

        $event = new OrderEvent($order);
        $event->setStatus(OrderStatusQuery::getPaidStatus()->getId());
        $this->getDispatcher()->dispatch($event, TheliaEvents::ORDER_UPDATE_STATUS);
    }

    /**
     * @return bool
     */
    public function manageStockOnCreation()
    {
        return false;
    }
}
