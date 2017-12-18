<?php declare(strict_types=1);

namespace Shopware\Api\Mail\Event\Mail;

use Shopware\Api\Mail\Collection\MailDetailCollection;
use Shopware\Api\Mail\Event\MailAttachment\MailAttachmentBasicLoadedEvent;
use Shopware\Api\Mail\Event\MailTranslation\MailTranslationBasicLoadedEvent;
use Shopware\Api\Order\Event\OrderState\OrderStateBasicLoadedEvent;
use Shopware\Context\Struct\TranslationContext;
use Shopware\Framework\Event\NestedEvent;
use Shopware\Framework\Event\NestedEventCollection;

class MailDetailLoadedEvent extends NestedEvent
{
    public const NAME = 'mail.detail.loaded';

    /**
     * @var TranslationContext
     */
    protected $context;

    /**
     * @var MailDetailCollection
     */
    protected $mails;

    public function __construct(MailDetailCollection $mails, TranslationContext $context)
    {
        $this->context = $context;
        $this->mails = $mails;
    }

    public function getName(): string
    {
        return self::NAME;
    }

    public function getContext(): TranslationContext
    {
        return $this->context;
    }

    public function getMails(): MailDetailCollection
    {
        return $this->mails;
    }

    public function getEvents(): ?NestedEventCollection
    {
        $events = [];
        if ($this->mails->getOrderStates()->count() > 0) {
            $events[] = new OrderStateBasicLoadedEvent($this->mails->getOrderStates(), $this->context);
        }
        if ($this->mails->getAttachments()->count() > 0) {
            $events[] = new MailAttachmentBasicLoadedEvent($this->mails->getAttachments(), $this->context);
        }
        if ($this->mails->getTranslations()->count() > 0) {
            $events[] = new MailTranslationBasicLoadedEvent($this->mails->getTranslations(), $this->context);
        }

        return new NestedEventCollection($events);
    }
}
