

document.getElementById('fom').addEventListener('submit', async function (e) {
  e.preventDefault();

  const form = e.target;
  const formData = new FormData(form);
  const messageDiv = document.getElementById('form-message');
  const spinner = document.getElementById('loadingSpinner');

  messageDiv.textContent = '';
  spinner.style.display = 'block';

  try {
    const response = await fetch(form.action + '?action=Register', {
      method: 'POST',
      body: formData
    });

    const result = await response.json();
    spinner.style.display = 'none';

    if (result.status === 'error') {
      messageDiv.style.color = '#b00020';
      messageDiv.textContent = result.message;
    } else if (result.status === 'success') {
      messageDiv.style.color = '#2e7d32';
      messageDiv.textContent = 'Signup successful!';

      const userType = formData.get('user_type');

      if (userType === 'Manager') {

        document.getElementById('businessModal').style.display = 'block';
      } else {

        setTimeout(() => {
          window.location.href = '/login.php';
        }, 1500);
      }
    }
  } catch (error) {
    spinner.style.display = 'none';
    messageDiv.textContent = 'An unexpected error occurred. Please try again.';
    console.error('Signup error:', error);
  }
});

document.getElementById('businessForm').addEventListener('submit', async function (e) {
    e.preventDefault();
  
    const form = e.target;
    const data = Object.fromEntries(new FormData(form).entries());
    const spinner = document.getElementById('businessSpinner');
  
    spinner.style.display = 'block';
  
    try {
      const response = await fetch('api.php?action=RegisterBusiness', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify(data)
      });
  
      const result = await response.json();
      spinner.style.display = 'none';
  
      if (result.success) {
        document.getElementById('businessModal').style.display = 'none';
        document.getElementById('businessSpinner').style.display = 'none';
        window.location.href = '/login.php';
        window.location.href = '/login.php';
      } else {
        alert('Error: ' + result.error);
      }
    } catch (err) {
      spinner.style.display = 'none';
      alert('Unexpected error during business registration');
      console.error(err);
    }
  });
  