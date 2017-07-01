<?php
/**
 * Created by PhpStorm.
 * User: marco
 * Date: 01.07.17
 * Time: 23:54
 */

namespace Typo3Api\Tca\Field;


use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This is basically an input field with useful defaults for storing phone numbers.
 * I've seen phone numbers entered in the owrst way possible way to often.
 * This prefix forces the user to store it correctly.
 * For output I reccommend giggsey/libphonenumber-for-php for formatting.
 */
class PhoneField extends InputField
{
    protected function configureOptions(OptionsResolver $resolver)
    {
        parent::configureOptions($resolver);
        $resolver->setDefaults([
            'max' => 31, // https://stackoverflow.com/a/4729239/1973256
            'is_in' => '+1234567890',
            'nospace' => true,
        ]);
    }
}