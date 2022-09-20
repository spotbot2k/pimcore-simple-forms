# Using AJAX

Any form can be send via AJAX if it is neccessery for your UX. You will have to implement a [custom controller](./021_Using_custom_controller.md) for it to work properly.

Create a simple JavaScript file

``` js
window.addEventListener('load', () => {
    let form = document.querySelector('#form_simple_1128');

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const data = new FormData(e.target);
        const value = Object.fromEntries(data.entries());
        const verb = e.target.getAttribute('method').toUpperCase();
        const action = e.target.getAttribute('action') || window.location.href;

        fetch(action, {
            method: verb,
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify(value),
        })
        .then((response) => response.json())
        .then((data) => {
            // Do what you want on success
            console.log('Success:', data);
        })
        .catch((error) => {
            console.error('Error:', error);
        });
    });
});
```

Obviously the id will be the one of your data object.

The reason why it is not a part of the bundle is the handling of the response - it is bound to your particular UX and UI.
