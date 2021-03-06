<?php
/**
 * gredu_labs
 *
 * @link https://github.com/eellak/gredu_labs for the canonical source repository
 * @copyright Copyright (c) 2008-2015 Greek Free/Open Source Software Society (https://gfoss.ellak.gr/)
 * @license GNU GPLv3 http://www.gnu.org/licenses/gpl-3.0-standalone.html
 */

namespace GrEduLabs\ReceiveEquip\InputFilter;

use Zend\Filter;
use Zend\InputFilter\CollectionInputFilter;
use Zend\InputFilter\Input;
use Zend\InputFilter\InputFilter;
use Zend\Validator;

class ReceiveEquip extends InputFilter
{
    public function __construct(
        CollectionInputFilter $itemsInputFilter
    ) {
        $id = new Input('id');
        $id->setRequired(true)
          ->getFilterChain()
          ->attach(new Filter\ToInt());
        $id->getValidatorChain()
          ->attach(new Validator\NotEmpty());

        $schoolId = new Input('school_id');
        $schoolId->setRequired(true)
            ->getFilterChain()
            ->attach(new Filter\ToInt());
        $schoolId->getValidatorChain()
            ->attach(new Validator\NotEmpty());

        $submittedBy = new Input('submitted_by');
        $submittedBy->setRequired(true)
            ->getValidatorChain()
            ->attach(new Validator\NotEmpty())
            ->attach(new Validator\EmailAddress([
                'useDomainCheck' => false,
            ]));

        $this->add($id)
            ->add($schoolId)
            ->add($submittedBy)
            ->add($itemsInputFilter, 'items');
    }
}
