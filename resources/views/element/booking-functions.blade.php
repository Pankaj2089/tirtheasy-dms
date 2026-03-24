<script>
    // Global booking modal functions

    // Global variables for payment management
    let bookingCharges = [];
    let bookingPayments = [];
    let currentBookingId = null;
    let currentBookingData = null;

    async function showBookingSummary(booking) {
        if (!booking) {
            console.error('No booking object provided');
            return;
        }

        // Check if booking has an ID
        if (!booking.id) {
            console.error('Booking object missing id:', booking);
            return;
        }

        // If it's a maintenance block, don't open booking popup - open maintenance popup instead
        if (booking.isBlocked || booking.status === 'maintenance' || booking.type === 'maintenance') {
            // showMaintenanceBlockDetails(booking); // Not available globally
            return;
        }

        // Open the booking popup modal
        const bookingModal = document.getElementById('booking-pop');
        if (!bookingModal) {
            console.error('Booking modal element not found');
            return;
        }

        // Show loading state
        populateBookingModal({ loading: true });

        // Open modal first
        try {
            if (window.bootstrap?.Modal) {
                const modalInstance = window.bootstrap.Modal.getOrCreateInstance(bookingModal);
                modalInstance.show();
            } else {
                bookingModal.classList.add('show');
                bookingModal.style.display = 'block';
                bookingModal.removeAttribute('aria-hidden');
            }
        } catch (modalError) {
            console.error('Error opening modal:', modalError);
        }

        // Fetch booking details
        try {
            const bookingId = encodeURIComponent(booking.id);
            // Build the endpoint URL - Laravel route helper needs proper replacement
            const baseUrl = `{{ url('/booking/') }}`;
            const bookingEndpoint = `${baseUrl}/ORD-${bookingId}`;

            const response = await fetch(bookingEndpoint, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });

            if (!response.ok) {
                const errorData = await response.json().catch(() => ({}));
                throw new Error(errorData.message || `Failed to fetch booking details: ${response.status}`);
            }

            const data = await response.json();
            if (data.success && data.booking) {
                populateBookingModal(data.booking);
            } else {
                throw new Error(data.message || 'Booking not found');
            }
        } catch (error) {
            console.error('Error fetching booking details:', error);
            populateBookingModal({ error: error.message || 'Failed to load booking details' });
        }
    }

    

    // Load booking payment history from API
    async function loadBookingPaymentHistory(bookingId) {
        if (!bookingId) return;
        bookingId = bookingId.replace('ORD-', ''); // Remove prefix if present
        currentBookingId = bookingId;
        
        try {
            const response = await fetch(`{{ url('/order-payments/') }}/${bookingId}`, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                }
            });
            
            if (!response.ok) {
                console.error('Failed to load payment history:', response.status);
                return;
            }
            
            const data = await response.json();
            
            // Reset arrays
            bookingCharges = [];
            bookingPayments = [];
            
            // Process charges
            if (data.charges && Array.isArray(data.charges)) {
                data.charges.forEach(charge => {
                    bookingCharges.push({
                        comment: charge.comment || 'Extra Charge',
                        amount: parseFloat(charge.amount) || 0,
                        display_date: charge.created_at ? new Date(charge.created_at).toLocaleString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        }) : new Date().toLocaleString('en-GB'),
                        existing: true
                    });
                });
            }
            
            // Process payments
            if (data.payments && Array.isArray(data.payments)) {
                data.payments.forEach(payment => {
                    bookingPayments.push({
                        type: payment.type || 'Payment',
                        method: payment.method || 'Cash',
                        comment: payment.comment || 'Payment',
                        amount: parseFloat(payment.amount) || 0,
                        display_date: payment.created_at ? new Date(payment.created_at).toLocaleString('en-GB', {
                            day: '2-digit',
                            month: '2-digit',
                            year: 'numeric',
                            hour: '2-digit',
                            minute: '2-digit',
                            hour12: true
                        }) : new Date().toLocaleString('en-GB'),
                        existing: true
                    });
                });
            }
            
            // Update UI
            renderBookingChargesList();
            renderBookingPaymentsList();
            renderBookingFolioHistory();
            recalcBookingSummary();
            
        } catch (error) {
            console.error('Error loading payment history:', error);
        }
    }

    // Render charges list
    function renderBookingChargesList() {
        const container = document.getElementById('bookingChargesList');
        if (!container) return;
        
        const newCharges = bookingCharges.filter(c => !c.existing);
        
        if (newCharges.length === 0) {
            container.innerHTML = '<p class="text-muted small mb-0">No new charges added</p>';
            return;
        }
        
        container.innerHTML = newCharges.map((charge, index) => `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                <div>
                    <strong>${charge.comment}</strong>
                    <br><small style="font-size: 12px;">${charge.display_date}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3">Rs. ${charge.amount.toFixed(2)}</span>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeBookingCharge(${bookingCharges.indexOf(charge)})">X</button>
                </div>
            </div>
        `).join('');
    }
    
    // Render payments list
    function renderBookingPaymentsList() {
        const container = document.getElementById('bookingPaymentsList');
        if (!container) return;
        
        const newPayments = bookingPayments.filter(p => !p.existing);
        
        if (newPayments.length === 0) {
            container.innerHTML = '<p class="text-muted small mb-0">No new payments added</p>';
            return;
        }
        
        container.innerHTML = newPayments.map((payment, index) => `
            <div class="d-flex justify-content-between align-items-center mb-2 p-2 bg-light rounded">
                <div>
                    <strong>${payment.type} - ${payment.method}</strong>
                    <br><small style="font-size: 12px;">${payment.display_date}</small>
                </div>
                <div class="d-flex align-items-center">
                    <span class="me-3">Rs. ${payment.amount.toFixed(2)}</span>
                    <button type="button" class="btn btn-sm btn-danger" onclick="removeBookingPayment(${bookingPayments.indexOf(payment)})">X</button>
                </div>
            </div>
        `).join('');
    }
    
    // Render folio history
    function renderBookingFolioHistory() {
        const container = document.getElementById('bookingFolioHistoryDetails');
        if (!container) return;
        
        const formatAmount = (amount) => {
            return parseFloat(amount).toFixed(2);
        };
        
        let html = '<ul class="list-unstyled">';
        
        // Show all charges (existing + new)
        bookingCharges.forEach((charge) => {
            html += `
            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                <div>
                    <p class="mb-0">${charge.comment}</p>
                    <small class="text-muted" style="font-size: 12px;">${charge.display_date}</small>
                </div>
                <p class="mb-0 text-danger"><strong>+ Rs. ${formatAmount(charge.amount)}</strong></p>
            </li>
            `;
        });
        
        // Show all payments (existing + new)
        bookingPayments.forEach((payment) => {
            html += `
            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                <div>
                    <p class="mb-0">${payment.type} - ${payment.method}</p>
                    <small class="text-muted" style="font-size: 12px;">${payment.display_date}</small>
                </div>
                <p class="mb-0 text-success">- Rs. ${formatAmount(payment.amount)}</p>
            </li>
            `;
        });
        
        html += '</ul>';
        
        if (bookingCharges.length === 0 && bookingPayments.length === 0) {
            html = '<p class="text-muted">No charges or payments recorded yet.</p>';
        }
        
        container.innerHTML = html;
    }

    function populateBookingModal(booking) {
        if (booking.loading) {
            document.getElementById('bookingGuestName').textContent = 'Loading...';
            return;
        }

        if (booking.error) {
            document.getElementById('bookingGuestName').textContent = booking.error;
            return;
        }

        // Set current booking data
        currentBookingData = booking;

        // Populate booking details
        document.getElementById('bookingModalBookingId').value = booking.id || '';
        document.getElementById('bookingGuestName').textContent = booking.guest_name || '-';
        document.getElementById('bookingFolio').textContent = booking.invoice_id || '-';
        document.getElementById('bookingDate').textContent = booking.created_at ? new Date(booking.created_at).toLocaleDateString() : '-';

        // Booking Details Tab
        document.getElementById('bookingReservationNumber').textContent = booking.invoice_id || '-';
        document.getElementById('bookingArrivalDate').textContent = booking.check_in_date ? new Date(booking.check_in_date).toLocaleDateString('en-GB') : '-';
        document.getElementById('bookingBookingDate').textContent = booking.created_at ? new Date(booking.created_at).toLocaleDateString('en-GB') : '-';
        document.getElementById('bookingDepartureDate').textContent = booking.check_out_date ? new Date(booking.check_out_date).toLocaleDateString('en-GB') : '-';
        document.getElementById('bookingRatePlan').textContent = booking.room_price ? `Rs. ${booking.room_price}` : '-';
        document.getElementById('bookingRoomNumber').textContent = booking.room_number_ids || '-';
        document.getElementById('bookingRoomType').textContent = booking.room_title || '-';
        document.getElementById('bookingNumberOfGuest').textContent = (booking.adults || 0) + (booking.childs || 0);
        document.getElementById('bookingRoomBooked').textContent = booking.rooms || '-';

        // Payment Details Tab
        document.getElementById('paymentFolio').textContent = booking.invoice_id || '-';
        document.getElementById('paymentDate').textContent = booking.created_at ? new Date(booking.created_at).toLocaleDateString() : '-';
        document.getElementById('paymentRoomPrice').textContent = booking.room_price ? `Rs. ${booking.room_price}` : 'Rs. 0';
        document.getElementById('paymentSGST').textContent = 'Rs. 0'; // Calculate if needed
        document.getElementById('paymentCGST').textContent = 'Rs. 0'; // Calculate if needed
        document.getElementById('paymentExtraCharges').textContent = booking.extra_mattress_price ? `Rs. ${booking.extra_mattress_price}` : 'Rs. 0';
        document.getElementById('paymentTotalPayable').textContent = booking.grand_total ? `Rs. ${booking.grand_total}` : 'Rs. 0';
        document.getElementById('paymentBalance').textContent = booking.grand_total ? `Rs. ${booking.grand_total}` : 'Rs. 0'; // Calculate balance

        // Guest Details Tab
        document.getElementById('guestName').textContent = booking.guest_name || '-';
        document.getElementById('guestEmail').textContent = booking.guest_email || '-';
        document.getElementById('guestPhone').textContent = booking.guest_phone || '-';
        document.getElementById('guestCity').textContent = booking.guest_city || '-';
        document.getElementById('guestState').textContent = booking.guest_state || '-';
        document.getElementById('guestAddress').textContent = booking.guest_address || '-';

        // Footer
        document.getElementById('footerTotal').textContent = booking.grand_total ? `Rs. ${booking.grand_total}` : 'Rs. 0';
        document.getElementById('footerPaid').textContent = 'Rs. 0.00'; // Calculate paid amount
        document.getElementById('footerBalance').textContent = booking.grand_total ? `Rs. ${booking.grand_total}` : 'Rs. 0'; // Calculate balance

        // Show/hide check-in/checkout buttons based on status
        const checkinBtn = document.getElementById('bookingCheckInBtn');
        const checkoutBtn = document.getElementById('bookingCheckoutBtn');
        if (booking.checkin_checkout_status === 'checked_in') {
            checkinBtn.style.display = 'none';
            checkoutBtn.style.display = 'block';
        } else {
            checkinBtn.style.display = 'block';
            checkoutBtn.style.display = 'none';
        }

        // Load payment history
        loadBookingPaymentHistory(booking.id);
    }

    function recalcBookingSummary() {
        if (!currentBookingData) return;
        
        const formatCurrency = (amount) => {
            if (!amount && amount !== 0) return 'Rs. 0';
            return `Rs. ${parseFloat(amount).toLocaleString('en-IN', { minimumFractionDigits: 2, maximumFractionDigits: 2 })}`;
        };
        
        // Calculate new charges and payments only (existing ones are already in grand_total)
        const newCharges = bookingCharges.filter(c => !c.existing);
        const newPayments = bookingPayments.filter(p => !p.existing);
        
        const totalNewCharges = newCharges.reduce((sum, c) => sum + c.amount, 0);
        const totalNewPayments = newPayments.reduce((sum, p) => sum + p.amount, 0);
        
        // Calculate total ALL charges (existing + new) for display
        const totalAllCharges = bookingCharges.reduce((sum, c) => sum + c.amount, 0);
        
        // Calculate total existing payments from database
        const totalExistingPayments = bookingPayments.filter(p => p.existing).reduce((sum, p) => sum + p.amount, 0);
        
        // Get original values
        const originalGrandTotal = parseFloat(currentBookingData.grand_total) || 0;
        
        // Calculate new totals
        const newGrandTotal = originalGrandTotal + totalNewCharges;
        // Balance = Grand Total - All Payments (existing + new)
        const newBalance = newGrandTotal - (totalExistingPayments + totalNewPayments);
        
        // Update Extra Charges display (show ALL charges - existing + new)
        const paymentExtraCharges = document.getElementById('paymentExtraCharges');
        if (paymentExtraCharges) {
            paymentExtraCharges.textContent = formatCurrency(totalAllCharges);
        }
        
        // Update Total Payable
        const paymentTotalPayable = document.getElementById('paymentTotalPayable');
        if (paymentTotalPayable) {
            paymentTotalPayable.textContent = formatCurrency(newGrandTotal);
        }
        
        // Update Balance
        const paymentBalance = document.getElementById('paymentBalance');
        if (paymentBalance) {
            paymentBalance.textContent = formatCurrency(newBalance);
        }
        
        // Auto-fill payment amount with balance
        const bookingPaymentAmountInput = document.getElementById('bookingPaymentAmount');
        if (bookingPaymentAmountInput && newBalance > 0) {
            bookingPaymentAmountInput.value = newBalance.toFixed(2);
        }
        
        // Calculate total ALL payments (existing + new) for footer display
        const totalAllPayments = bookingPayments.reduce((sum, p) => sum + p.amount, 0);
        
        // Update footer
        const footerTotal = document.getElementById('footerTotal');
        const footerPaid = document.getElementById('footerPaid');
        const footerBalance = document.getElementById('footerBalance');
        
        if (footerTotal) footerTotal.textContent = formatCurrency(newGrandTotal);
        if (footerPaid) footerPaid.textContent = formatCurrency(totalAllPayments);
        if (footerBalance) footerBalance.textContent = formatCurrency(newBalance);
    }

    // Remove charge
    window.removeBookingCharge = function(index) {
        bookingCharges.splice(index, 1);
        renderBookingChargesList();
        renderBookingFolioHistory();
        recalcBookingSummary();
    };
    
    // Remove payment
    window.removeBookingPayment = function(index) {
        bookingPayments.splice(index, 1);
        renderBookingPaymentsList();
        renderBookingFolioHistory();
        recalcBookingSummary();
    };

    // Placeholder functions - implement as needed
    function checkInBooking() {
        alert('Check-in functionality not implemented globally');
    }

    function checkoutBooking() {
        alert('Checkout functionality not implemented globally');
    }

    function saveBookingChargesPayments() {
        if (!currentBookingId) {
            if (typeof swal !== 'undefined') {
                swal("Error!", 'No booking selected');
            } else {
                alert('No booking selected');
            }
            return false;
        }

        if (!currentBookingData || currentBookingData.type === 'maintenance') {
            if (typeof swal !== 'undefined') {
                swal("Error!", 'Cannot add charges or payments to maintenance blocks');
            } else {
                alert('Cannot add charges or payments to maintenance blocks');
            }
            return false;
        }

        const newCharges = bookingCharges.filter(c => !c.existing);
        const newPayments = bookingPayments.filter(p => !p.existing);

        if (newCharges.length === 0 && newPayments.length === 0) {
            if (typeof swal !== 'undefined') {
                swal("Error!", 'No new charges or payments to save');
            } else {
                alert('No new charges or payments to save');
            }
            return false;
        }

        // Send data to server
        const payload = {
            order_id: currentBookingId,
            charges: newCharges,
            payments: newPayments
        };

        fetch('{{ route("booking.saveChargesPayments") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                'Accept': 'application/json'
            },
            body: JSON.stringify(payload)
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                if (typeof swal !== 'undefined') {
                    swal("Success!", 'Charges and payments saved successfully!');
                } else {
                    alert('Charges and payments saved successfully!');
                }
                // Reload the payment history to show all as existing
                loadBookingPaymentHistory(currentBookingId);
                return true;
            } else {
                let errorMessage = data.message || 'Failed to save charges and payments';
                
                // Show validation errors if available
                if (data.errors) {
                    const errorDetails = Object.values(data.errors).flat().join('\n');
                    errorMessage += '\n\nValidation Errors:\n' + errorDetails;
                }
                
                // Show technical error if available
                if (data.error) {
                    errorMessage += '\n\nError: ' + data.error;
                    if (data.file && data.line) {
                        errorMessage += '\nFile: ' + data.file + ' (Line ' + data.line + ')';
                    }
                }
                
                if (typeof swal !== 'undefined') {
                    swal("Error!", errorMessage);
                } else {
                    alert(errorMessage);
                }
                return false;
            }
        })
        .catch(error => {
            console.error('Error saving charges and payments:', error);
            if (typeof swal !== 'undefined') {
                swal("Error!", 'An error occurred while saving: ' + error.message);
            } else {
                alert('An error occurred while saving: ' + error.message);
            }
            return false;
        });
    }

    // Event listeners for add charge and payment buttons
    document.addEventListener('DOMContentLoaded', function() {
        // Add Charge Button
        const chargeAddBtn = document.getElementById('bookingChargeAddBtn');
        if (chargeAddBtn) {
            chargeAddBtn.addEventListener('click', function() {
                const commentInput = document.getElementById('bookingChargeComment');
                const amountInput = document.getElementById('bookingChargeAmount');
                
                const comment = commentInput.value.trim();
                const amount = parseFloat(amountInput.value);
                
                if (!comment) {
                    if (typeof swal !== 'undefined') {
                        swal("Error!", 'Please enter a comment');
                    } else {
                        alert('Please enter a comment');
                    }
                    return;
                }
                
                if (!amount || amount <= 0) {
                    if (typeof swal !== 'undefined') {
                        swal("Error!", 'Please enter a valid amount');
                    } else {
                        alert('Please enter a valid amount');
                    }
                    return;
                }
                
                bookingCharges.push({
                    comment,
                    amount,
                    display_date: new Date().toLocaleString('en-GB', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    }),
                    existing: false
                });
                
                commentInput.value = '';
                amountInput.value = '';
                
                renderBookingChargesList();
                renderBookingFolioHistory();
                recalcBookingSummary();
            });
        }
        
        // Add Payment Button
        const paymentAddBtn = document.getElementById('bookingPaymentAddBtn');
        if (paymentAddBtn) {
            paymentAddBtn.addEventListener('click', function() {
                const typeSelect = document.getElementById('bookingPaymentType');
                const methodSelect = document.getElementById('bookingPaymentMethod');
                const commentInput = document.getElementById('bookingPaymentComment');
                const amountInput = document.getElementById('bookingPaymentAmount');
                
                const type = typeSelect.value;
                const method = methodSelect.value;
                const comment = commentInput.value.trim();
                const amount = parseFloat(amountInput.value);
                
                if (!type) {
                    if (typeof swal !== 'undefined') {
                        swal("Error!", 'Please select a payment type');
                    } else {
                        alert('Please select a payment type');
                    }
                    return;
                }
                
                if (!method) {
                    if (typeof swal !== 'undefined') {
                        swal("Error!", 'Please select a payment method');
                    } else {
                        alert('Please select a payment method');
                    }
                    return;
                }
                
                if (!amount || amount <= 0) {
                    if (typeof swal !== 'undefined') {
                        swal("Error!", 'Please enter a valid amount');
                    } else {
                        alert('Please enter a valid amount');
                    }
                    return;
                }
                
                bookingPayments.push({
                    type,
                    method,
                    comment: comment || 'Payment',
                    amount,
                    display_date: new Date().toLocaleString('en-GB', {
                        day: '2-digit',
                        month: '2-digit',
                        year: 'numeric',
                        hour: '2-digit',
                        minute: '2-digit',
                        hour12: true
                    }),
                    existing: false
                });
                
                typeSelect.value = '';
                methodSelect.value = '';
                methodSelect.disabled = true;
                commentInput.value = '';
                amountInput.value = '';
                
                renderBookingPaymentsList();
                renderBookingFolioHistory();
                recalcBookingSummary();
            });
        }
        
        // Payment type change handler
        const paymentTypeSelect = document.getElementById('bookingPaymentType');
        if (paymentTypeSelect) {
            paymentTypeSelect.addEventListener('change', function() {
                const methodSelect = document.getElementById('bookingPaymentMethod');
                if (methodSelect) {
                    methodSelect.innerHTML = '<option value="">Select Method</option>';
                    
                    if (this.value === 'Cash') {
                        methodSelect.innerHTML += '<option value="Cash">Cash</option>';
                    } else if (this.value === 'Online') {
                        methodSelect.innerHTML += `
                            <option value="Card">Card</option>
                            <option value="UPI">UPI</option>
                            <option value="Bank Transfer">Bank Transfer</option>
                            <option value="Cheque">Cheque</option>
                        `;
                    }
                    
                    methodSelect.disabled = !this.value;
                    if (!this.value) {
                        methodSelect.value = '';
                    }
                }
            });
        }
    });
</script>