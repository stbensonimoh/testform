(() => {
    'use strict';

    const submit = document.getElementById('register');
    
    submit.addEventListener('click',()=>{
        // stop the button from submitting
        event.preventDefault(); 

        // grab the form fields
        let name = document.getElementById('name').value;
        let email = document.getElementById('email').value;
        let phone = document.getElementById('phone').value;
        let amount = document.getElementById('amount').value * 100;

        let postData = JSON.stringify({
            name: name,
            email: email,
            phone: phone,
            amount: amount
        })

        fetch('paynow.php', {
            method: 'post',
            mode: "same-origin",
            credentials: "same-origin",
            body: postData
        })
        .then((response)=> {
            return response.json();            
        })
        .then((data)=>{
            console.log(data);
        })
        .catch((error)=>{
            console.log('The Request Failed', error);
        })

    });
}
)();