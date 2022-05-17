# Basic Configuration

Lets consider the most simple scenario - a form that sends a message to the owner of the website and a copy to the sender.

1. Create a form with the input fields you need. You will need at leaset one simple text input for the email. Lets give it the label "Email"
2. Create an email document for the copy, something like "Your message has been recieved". To deliver this message to the correct email put `{{ email }}` into the "TO" field. This is the slug of the label of the field, it will be replaced with the value of the field before the message will be sent.
3. If you do not have an E-Mail controller in your setup you can use the basic one provided in this bundle - in the document settings put `SimpleFormsBundle\Controller\EmailController::mailAction` into the "Controller" field.
4. Create an email document for the site owner. In the text of the mail use `{{ %slug% }}` placeholder to insert field values (just as described above). **OR** you can just use the tag `{{formValue|nl2br}}` to output all the data. In this case the file path will not be visible, just the name.
5. Drag you email documents onto the output tab of the form object.
6. Use the "Simple Form Object" aeriabrick on any page to render the form.
7. Done

**Warning!**: The provided email controller will not work in pimcore 6.9, you will need to implement your own.
