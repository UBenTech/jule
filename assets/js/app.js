/**
 * Intelligent Pharmacy Management System
 * Main JavaScript File
 *
 * Contains client-side logic for AJAX interactions using the Fetch API.
 * Handles wishlist additions, booking submissions, and stock checks.
 */

document.addEventListener('DOMContentLoaded', function() {

    /**
     * Handles clicks on 'Add to Wishlist' buttons.
     */
    document.querySelectorAll('.btn-wishlist').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            const medicineId = this.dataset.medicineId;
            addToWishlist(medicineId);
        });
    });

    /**
     * Handles the submission of the booking form.
     */
    const bookingForm = document.getElementById('booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            submitBooking(bookingForm);
        });
    }

    /**
     * Handles the deletion of a medicine from the admin panel.
     */
    document.querySelectorAll('.btn-delete-medicine').forEach(button => {
        button.addEventListener('click', function(e) {
            e.preventDefault();
            if (confirm('Are you sure you want to delete this medicine? This action cannot be undone.')) {
                const medicineId = this.dataset.medicineId;
                deleteMedicine(medicineId, this);
            }
        });
    });

});


/**
 * Sends a request to the API to add a medicine to the wishlist.
 * @param {number} medicineId - The ID of the medicine to add.
 */
async function addToWishlist(medicineId) {
    console.log(`Adding medicine ${medicineId} to wishlist...`);
    const responseDiv = document.getElementById(`wishlist-response-${medicineId}`);

    try {
        const response = await fetch(`${BASE_URL}/api.php?action=wishlist_add`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ medicine_id: medicineId })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            alert('Added to wishlist!');
            // You could also update a wishlist icon/counter here
        } else {
            throw new Error(result.message || 'Could not add to wishlist.');
        }

    } catch (error) {
        alert(`Error: ${error.message}`);
        console.error('Wishlist Error:', error);
    }
}


/**
 * Submits the booking form data to the API.
 * @param {HTMLFormElement} formElement - The booking form element.
 */
async function submitBooking(formElement) {
    const formData = new FormData(formElement);
    const data = Object.fromEntries(formData.entries());
    const responseDiv = document.getElementById('booking-response');
    responseDiv.textContent = 'Submitting...';

    try {
        const response = await fetch(`${BASE_URL}/api.php?action=book`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify(data)
        });

        const result = await response.json();

        if (response.ok && result.success) {
            responseDiv.style.color = 'green';
            responseDiv.textContent = 'Booking successful! We will contact you shortly.';
            formElement.reset();
        } else {
            throw new Error(result.message || 'Booking failed. Please try again.');
        }

    } catch (error) {
        responseDiv.style.color = 'red';
        responseDiv.textContent = `Error: ${error.message}`;
        console.error('Booking Error:', error);
    }
}

/**
 * Sends a request to the API to delete a medicine.
 * @param {number} medicineId - The ID of the medicine to delete.
 * @param {HTMLElement} buttonElement - The button that was clicked.
 */
async function deleteMedicine(medicineId, buttonElement) {
    console.log(`Deleting medicine ${medicineId}...`);

    try {
        const response = await fetch(`${BASE_URL}/api.php?action=med_delete`, {
            method: 'POST',
            headers: { 'Content-Type': 'application/json' },
            body: JSON.stringify({ medicine_id: medicineId })
        });

        const result = await response.json();

        if (response.ok && result.success) {
            // Remove the table row from the DOM
            const row = buttonElement.closest('tr');
            row.style.transition = 'opacity 0.5s ease';
            row.style.opacity = '0';
            setTimeout(() => row.remove(), 500);
            alert('Medicine deleted successfully.');
        } else {
            throw new Error(result.message || 'Failed to delete medicine.');
        }

    } catch (error) {
        alert(`Error: ${error.message}`);
        console.error('Delete Error:', error);
    }
}


/**
 * A placeholder for a stock check function.
 * This could be called on a quantity input change, for example.
 * @param {number} medicineId
 */
async function checkStock(medicineId) {
    // Example implementation:
    // const response = await fetch(`/api.php?action=stock_check&id=${medicineId}`);
    // const result = await response.json();
    // if (result.available) { ... }
    console.log(`Stock check function called for medicine ${medicineId}.`);
}
