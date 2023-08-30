<?php

namespace Jabe\Impl\History\Handler;

use Jabe\Impl\History\Event\HistoryEvent;
use Jabe\Impl\Util\EnsureUtil;

class CompositeHistoryEventHandler implements HistoryEventHandlerInterface
{
    /**
     * The list of HistoryEventHandler which consume the event.
     */
    protected $historyEventHandlers = [];

    /**
     * Constructor that takes a varargs parameter HistoryEventHandler that
     * consume the event.
     *
     * @param historyEventHandlers
     *          the list of HistoryEventHandler that consume the event.
     */
    public function __construct(?array $historyEventHandlers = [])
    {
        if (!empty($historyEventHandlers)) {
            $this->initializeHistoryEventHandlers($historyEventHandlers);
        }
    }

    /**
     * Initialize {@link #historyEventHandlers} with data transfered from constructor
     *
     * @param historyEventHandlers
     */
    private function initializeHistoryEventHandlers(array $historyEventHandlers): void
    {
        EnsureUtil::ensureNotNull("History event handler", "historyEventHandlers", $historyEventHandlers);
        foreach ($historyEventHandlers as $historyEventHandler) {
            EnsureUtil::ensureNotNull("History event handler", "historyEventHandlers", $historyEventHandler);
            $this->historyEventHandlers[] = $historyEventHandler;
        }
    }

    /**
     * Adds the HistoryEventHandler to the list of
     * HistoryEventHandler that consume the event.
     *
     * @param historyEventHandler
     *          the HistoryEventHandlerInterface that consume the event.
     */
    public function add(HistoryEventHandlerInterface $historyEventHandler): void
    {
        EnsureUtil::ensureNotNull("History event handler", "historyEventHandler", $historyEventHandler);
        $this->historyEventHandlers[] = $historyEventHandler;
    }

    public function handleEvent(HistoryEvent $historyEvent): void
    {
        foreach ($this->historyEventHandlers as $historyEventHandler) {
            $historyEventHandler->handleEvent($historyEvent);
        }
    }

    public function handleEvents(array $historyEvents): void
    {
        foreach ($historyEvents as $historyEvent) {
            $this->handleEvent($historyEvent);
        }
    }
}
