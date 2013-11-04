<?php

namespace wiosloCMS\PhotoBundle\Model;

use wiosloCMS\PhotoBundle\Model\om\BasePhoto;

class Photo extends BasePhoto
{
    public function getRating(\PropelPDO $con = null)
    {
        $rating = parent::getRating($con);

        if (!$rating instanceof Rating) {
            $rating = new Rating();
            $rating->setValue(0);
            parent::setRating($rating);
        }

        return $rating;
    }
}
