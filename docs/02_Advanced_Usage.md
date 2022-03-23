# Advanced Example

## Using Events

The Bundle provides two events - `PreSendMailEvent` where you can handle the raw form request and `PostSendMailEvent` where you can do something with the parsed data. Here is a example usage:

Craeate `src/EventListener/ContactFormListener.php` in your Pimcore instalaltion with folowing content:

``` php
<?php

namespace App\EventListener;

use SimpleFormsBundle\Event\PostSendMailEvent;
use Pimcore\Log\Simple as Log;

class ContactFormListener
{
    public function onFormSend(PostSendMailEvent $event)
    {
        Log::log('contact-form', $event->getData()['email']);
    }
}
```

We assume that the form has a field `email`. This listener will write the sender address in a log file for each sender. To register the listener add

``` yaml
    App\EventListener\ContactFormListener:
        tags:
            - { name: kernel.event_listener, event: simple_forms.post_send_mail, method: onFormSend }
```

to your `config/services.yaml`. Thats it.

## Complex data

Some of the data in the form may not be transformed into the plain text for an email. The files for example will remain in form of Pimcore assets and you will need to loop over them in your twig templates or listeners and generate the readable form yourself. The same goes for mmultiple choise, provided it is not in form of radio input.

## Futhter topics

- [How to Override any Part of a Bundle](https://symfony.com/doc/current/bundles/override.html)
- [How to Customize Form Rendering](https://symfony.com/doc/current/form/form_customization.html)
