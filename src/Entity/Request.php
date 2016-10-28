<?php

namespace RhMachine\Entity;

use CCMBenchmark\Ting\Entity\NotifyProperty;
use CCMBenchmark\Ting\Entity\NotifyPropertyInterface;
use RhMachine\Enum\StatusEnum;

class Request implements NotifyPropertyInterface
{
    use NotifyProperty;

    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $contact;

    /**
     * @var string
     */
    private $source;

    /**
     * @var StatusEnum
     */
    private $status;

    /**
     * @var \DateTime
     */
    private $creationDate;

    /**
     * @var \DateTime
     */
    private $modificationDate;

    public function __construct(int $id, string $contact, string $source)
    {
        $this->id = $id;
        $this->contact = $contact;
        $this->source = $source;
        $this->status = StatusEnum::PENDING;
        $this->modificationDate = new \DateTime();
        $this->creationDate = new \DateTime();
    }


}