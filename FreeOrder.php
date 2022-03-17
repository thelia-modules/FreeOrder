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
use Thelia\Model\Order;
use Thelia\Model\OrderStatusQuery;
use Thelia\Module\AbstractPaymentModule;

class FreeOrder extends AbstractPaymentModule
{
    /**
     * @return bool
     */
    public function isValidPayment()
    {
        return round($this->getCurrentOrderTotalAmount(), 4) == 0;
    }

    public function pay(Order $order): void
    {
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
