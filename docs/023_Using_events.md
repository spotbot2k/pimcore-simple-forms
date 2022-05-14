# Using Events

The Bundle provides two events - `PreSendMailEvent` where you can handle the raw form request and `PostSendMailEvent` where you can do something with the parsed data. Here is a example usage:

Create `src/EventListener/ContactFormListener.php` in your Pimcore installation with following content:

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
