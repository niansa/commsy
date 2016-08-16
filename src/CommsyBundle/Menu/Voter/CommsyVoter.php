<?php

namespace CommsyBundle\Menu\Voter;

use Knp\Menu\ItemInterface;
use Knp\Menu\Matcher\Voter\VoterInterface;
use Symfony\Component\HttpFoundation\RequestStack;

/**
 * Voter based on the uri
 */
class CommsyVoter implements VoterInterface
{
    private $requestStack;

    public function __construct(RequestStack $requestStack)
    {
        $this->requestStack = $requestStack;
    }

    public function matchItem(ItemInterface $item)
    {
        $roomId = $this->requestStack->getCurrentRequest()->attributes->get('roomId');
        $controllerArray = explode('_', $this->requestStack->getCurrentRequest()->attributes->get('_route'));
        
        if (stristr($item->getUri(), 'room/'.$roomId.'/'.$controllerArray[1])) {
            return true;
        }

        return false;
    }
}