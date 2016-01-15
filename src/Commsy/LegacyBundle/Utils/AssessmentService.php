<?php
namespace Commsy\LegacyBundle\Utils;

use Symfony\Component\Form\Form;

use Commsy\LegacyBundle\Services\LegacyEnvironment;

class AssessmentService
{
    private $legacyEnvironment;

    private $assessmentManager;
    

    public function __construct(LegacyEnvironment $legacyEnvironment)
    {
        $this->legacyEnvironment = $legacyEnvironment->getEnvironment();

        $this->assessmentManager = $this->legacyEnvironment->getAssessmentManager();
        $this->assessmentManager->reset();
    }

    public function getListAverageRatings($itemIds)
    {
        return $this->assessmentManager->getAssessmentForItemAverageByIDArray($itemIds);
    }
    
    public function getRatingDetail($item)
    {
        return $this->assessmentManager->getAssessmentForItemDetail($item);
    }
    
    public function getAverageRatingDetail($item)
    {
        return $this->assessmentManager->getAssessmentForItemAverage($item);
    }
    
    public function getOwnRatingDetail($item)
    {
        return $this->assessmentManager->getAssessmentForItemOwn($item);
    }
}