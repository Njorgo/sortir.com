<?php

namespace App\Filtre;

use App\Entity\Campus;

class FiltreClass
{
    public ?string $motCle = null;

    public ?Campus $campus= null;

    public ?\DateTime $dateMini= null;

    public ?\DateTime $dateMax = null;

    public bool $sortiesFinies = false;

    public bool $estOrganisee = false;

    public bool $estInscrit = false;

    public bool $nonInscrit = false;

}