# Quick Reservation Popup - Functional Requirements Implementation

## Overview
This document details the implementation of charge and payment management for both Quick Reservation (new bookings) and Existing Booking Management (booking-pop modal).

---

## Table of Contents
1. [Quick Reservation - New Bookings](#quick-reservation---new-bookings)
2. [Booking Management - Existing Bookings](#booking-management---existing-bookings)

---

---

# Quick Reservation - New Bookings

## General Flow

### Data Persistence
- **Data is saved to the database only when the Confirm button is clicked on the second tab (Payment Details)**
- Form data is held in temporary state (JavaScript arrays) until final confirmation
- All data (booking details, charges, and payments) is submitted together in a single API call

### Navigation Flow
1. User fills in booking details on the first tab (Normal Booking)
2. User clicks **Payment** button → automatically opens Payment Details tab
3. User adds charges and payments on the second tab
4. User clicks **Confirm** button → submits complete reservation with all data

---

## Step 1: Normal Booking Tab

### Changes Made

#### Button Change
- **Old**: "Confirm" submit button
- **New**: "Payment" button (type: button)
- **Functionality**: Automatically switches to Payment Details tab when clicked
- **Validation**: Guest Information (Name and Mobile) is mandatory before proceeding

```html
<button type="button" class="theme-btn text-white ms-auto text-center text-decoration-none" id="quickReservationPaymentBtn">
    Payment
</button>
```

#### JavaScript Implementation
```javascript
// Payment button - auto-open Payment Details tab with validation
if (quickPaymentBtn && paymentDetailsTab) {
    quickPaymentBtn.addEventListener('click', function() {
        // Validate Guest Information
        const guestName = document.getElementById('quickGuestName')?.value.trim();
        const guestMobile = document.getElementById('quickGuestMobile')?.value.trim();
        
        if (!guestName) {
            alert('Please enter guest name before proceeding to payment.');
            document.getElementById('quickGuestName')?.focus();
            return;
        }
        
        if (!guestMobile) {
            alert('Please enter guest mobile number before proceeding to payment.');
            document.getElementById('quickGuestMobile')?.focus();
            return;
        }
        
        // Trigger Payment Details tab
        const tabTrigger = new bootstrap.Tab(paymentDetailsTab);
        tabTrigger.show();
    });
}
```

#### Guest Information Validation
**Mandatory Fields:**
- **Guest Name** - Required
- **Guest Mobile** - Required

**Optional Fields:**
- Gender
- Email

When the user clicks the "Payment" button:
1. System checks if Guest Name is filled
2. If empty, shows alert: "Please enter guest name before proceeding to payment."
3. System checks if Guest Mobile is filled
4. If empty, shows alert: "Please enter guest mobile number before proceeding to payment."
5. Only if both are filled, the Payment Details tab opens

---

## Step 2: Payment Details Tab

### Payment Details Tab Disabled State
- **Initial State**: Disabled when no Room Type is selected
- **Enabled**: When Room Type is selected on Step 1
- **Visual Feedback**: Opacity 0.5 and pointer-events disabled when inactive

```javascript
function updatePaymentDetailsTabState() {
    const paymentDetailsTab = document.getElementById('nav-profile-tab');
    const roomTypeSelect = detailsContainer.querySelector('.room-type-select');
    const isRoomTypeSelected = roomTypeSelect && roomTypeSelect.value;
    
    if (isRoomTypeSelected) {
        paymentDetailsTab.classList.remove('disabled');
        paymentDetailsTab.removeAttribute('aria-disabled');
        paymentDetailsTab.style.pointerEvents = '';
        paymentDetailsTab.style.opacity = '';
    } else {
        paymentDetailsTab.classList.add('disabled');
        paymentDetailsTab.setAttribute('aria-disabled', 'true');
        paymentDetailsTab.style.pointerEvents = 'none';
        paymentDetailsTab.style.opacity = '0.5';
    }
}
```

---

## Add Charges Section

### Fields

#### Removed Fields
- ~~Room Charges~~ (select)
- ~~Room Quantity~~ (select)

#### Current Fields
- **Comment** (Text input) - Description of the charge
- **Amount** (Number input) - Charge amount with decimal support

### HTML Structure
```html
<div class="p-details">
    <h5 class="mt-3 mb-2">Add Charges</h5>
    <div class="row align-items-center mb-3">
        <div class="col-md-6 d-flex flex-column mb-3">
            <div class="shadow-sm p-2 rounded bg-light border">
                <label for="quickChargeComment" class="form-label mt-0">Comment</label>
                <input type="text" id="quickChargeComment" class="form-control bg-white" placeholder="Enter comment">
            </div>
        </div>
        <div class="col-md-6 d-flex flex-column mb-3">
            <div class="shadow-sm p-2 rounded bg-light border">
                <label for="quickChargeAmount" class="form-label mt-0">Amount</label>
                <input type="number" id="quickChargeAmount" class="form-control bg-white" placeholder="0.00" step="0.01" min="0">
            </div>
        </div>

        <div class="col-md-12 d-flex align-items-center justify-content-end">
            <div class="bl-btn">
                <button type="button" id="quickChargeAddBtn" class="theme-btn-sm rounded text-white text-center text-decoration-none border-0">Add Charge</button>
            </div>
        </div>

        <!-- Added Charges List -->
        <div class="col-md-12 mt-3">
            <div id="quickChargesList"></div>
        </div>
    </div>
</div>
```

### Functionality
- **Multiple Charges**: User can add multiple charges
- **Dynamic List**: Each charge appears in a list with delete button
- **Real-time Calculation**: Balance updates immediately when charge is added/removed

### JavaScript Implementation
```javascript
const quickCharges = [];

// Add Charge
chargeAddBtn.addEventListener('click', function() {
    const comment = chargeCommentInput?.value.trim();
    const amount = parseFloat(chargeAmountInput?.value || 0);
    
    // Validation
    if (!comment) {
        alert('Please enter a comment for the charge.');
        return;
    }
    if (amount <= 0) {
        alert('Please enter a valid amount greater than 0.');
        return;
    }
    
    // Add to array
    const charge = {
        id: Date.now(),
        comment: comment,
        amount: amount,
        date: new Date().toLocaleDateString('en-GB')
    };
    quickCharges.push(charge);
    
    // Clear inputs and update UI
    chargeCommentInput.value = '';
    chargeAmountInput.value = '';
    renderCharges();
    updatePaymentCalculations();
});

// Remove Charge
window.removeCharge = function(chargeId) {
    const index = quickCharges.findIndex(c => c.id === chargeId);
    if (index > -1) {
        quickCharges.splice(index, 1);
        renderCharges();
        updatePaymentCalculations();
    }
};
```

### Database Storage
- **Table**: `order_payments`
- **Fields**:
  - `order_id` - Links to the created order
  - `type` = "Charge"
  - `comment` - User's comment
  - `amount` - Charge amount
  - `date` - Date of charge

---

## Add Payment Section

### Fields

#### Type Dropdown
- **Options**: Cash / Online
- **Behavior**: Controls the Method dropdown

#### Method Dropdown
- **If Type = Cash**: 
  - Shows "Cash" option only
  - Automatically selects "Cash"
  - Disabled (non-editable)
  - Background color: #e9ecef
  
- **If Type = Online**:
  - Enabled for selection
  - Options: UPI / Card / Net Banking
  - User must select one

#### Additional Fields
- **Comment** (Text input) - Optional payment note
- **Amount** (Number input) - Payment amount with decimal support

### HTML Structure
```html
<div class="p-details">
    <h5 class="mt-3 mb-2">Add Payment</h5>
    <div class="row align-items-center mb-3">
        <div class="col-md-6 d-flex flex-column mb-3">
            <div class="shadow-sm p-2 rounded bg-light border">
                <label for="quickPaymentType" class="form-label mt-0">Type</label>
                <select id="quickPaymentType" class="form-control bg-white">
                    <option value="">Select Type</option>
                    <option value="Cash">Cash</option>
                    <option value="Online">Online</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 d-flex flex-column mb-3">
            <div class="shadow-sm p-2 rounded bg-light border">
                <label for="quickPaymentMethod" class="form-label mt-0">Method</label>
                <select id="quickPaymentMethod" class="form-control bg-white" disabled>
                    <option value="">Select Method</option>
                </select>
            </div>
        </div>
        <div class="col-md-6 d-flex flex-column mb-3">
            <div class="shadow-sm p-2 rounded bg-light border">
                <label for="quickPaymentComment" class="form-label mt-0">Comment</label>
                <input type="text" id="quickPaymentComment" placeholder="Add comment..." class="form-control bg-white">
            </div>
        </div>

        <div class="col-md-6 d-flex flex-column mb-3">
            <div class="shadow-sm p-2 rounded bg-light border">
                <label for="quickPaymentAmount" class="form-label mt-0">Amount</label>
                <input type="number" id="quickPaymentAmount" class="form-control bg-white" placeholder="0.00" step="0.01" min="0">
            </div>
        </div>

        <div class="col-md-12 d-flex align-items-center justify-content-end">
            <div class="bl-btn">
                <button type="button" id="quickPaymentAddBtn" class="theme-btn-sm rounded text-white text-center text-decoration-none border-0">Add Payment</button>
            </div>
        </div>

        <!-- Added Payments List -->
        <div class="col-md-12 mt-3">
            <div id="quickPaymentsList"></div>
        </div>
    </div>
</div>
```

### Functionality
- **Multiple Payments**: User can add multiple payment entries
- **Dynamic List**: Each payment appears in a list with type, method, and delete button
- **Real-time Calculation**: Balance updates immediately when payment is added/removed
- **Confirm Button Control**: Confirm button is disabled until at least one payment exists

### JavaScript Implementation
```javascript
const quickPayments = [];

// Type/Method Logic
paymentTypeSelect.addEventListener('change', function() {
    const type = this.value;
    paymentMethodSelect.innerHTML = '<option value="">Select Method</option>';
    
    if (type === 'Cash') {
        paymentMethodSelect.innerHTML = '<option value="Cash">Cash</option>';
        paymentMethodSelect.value = 'Cash';
        paymentMethodSelect.disabled = true;
        paymentMethodSelect.style.backgroundColor = '#e9ecef';
    } else if (type === 'Online') {
        paymentMethodSelect.innerHTML = `
            <option value="">Select Method</option>
            <option value="UPI">UPI</option>
            <option value="Card">Card</option>
            <option value="Net Banking">Net Banking</option>
        `;
        paymentMethodSelect.disabled = false;
        paymentMethodSelect.style.backgroundColor = '';
    } else {
        paymentMethodSelect.disabled = true;
        paymentMethodSelect.style.backgroundColor = '#e9ecef';
    }
});

// Add Payment
paymentAddBtn.addEventListener('click', function() {
    const type = paymentTypeSelect?.value;
    const method = paymentMethodSelect?.value;
    const comment = paymentCommentInput?.value.trim();
    const amount = parseFloat(paymentAmountInput?.value || 0);
    
    // Validation
    if (!type) {
        alert('Please select a payment type.');
        return;
    }
    if (!method) {
        alert('Please select a payment method.');
        return;
    }
    if (amount <= 0) {
        alert('Please enter a valid amount greater than 0.');
        return;
    }
    
    // Add to array
    const payment = {
        id: Date.now(),
        type: type,
        method: method,
        comment: comment || '-',
        amount: amount,
        date: new Date().toLocaleDateString('en-GB')
    };
    quickPayments.push(payment);
    
    // Clear inputs and update UI
    paymentTypeSelect.value = '';
    paymentMethodSelect.value = '';
    paymentMethodSelect.disabled = true;
    paymentMethodSelect.style.backgroundColor = '#e9ecef';
    paymentCommentInput.value = '';
    paymentAmountInput.value = '';
    
    renderPayments();
    updatePaymentCalculations();
    updateConfirmButtonState();
});

// Remove Payment
window.removePayment = function(paymentId) {
    const index = quickPayments.findIndex(p => p.id === paymentId);
    if (index > -1) {
        quickPayments.splice(index, 1);
        renderPayments();
        updatePaymentCalculations();
        updateConfirmButtonState();
    }
};
```

### Database Storage
- **Table**: `order_payments`
- **Fields**:
  - `order_id` - Links to the created order
  - `type` = "Payment"
  - `payment_type` - Cash or Online
  - `payment_method` - Cash / UPI / Card / Net Banking
  - `comment` - User's comment
  - `amount` - Payment amount
  - `date` - Date of payment

---

## Charges & Payment Details Section

### Display Information
This section (previously called "Folio History") displays a comprehensive view of all transactions and totals:

#### Transaction History (Detailed List)
Shows individual charges and payments as rows:
- **Charges**: 
  - Comment/Description
  - Date
  - Amount (shown in red with + prefix)
- **Payments**:
  - Type (Cash/Online)
  - Method (Cash/UPI/Card/Net Banking)
  - Comment
  - Date
  - Amount (shown in green with - prefix)

#### Summary Section (After a separator)
Displays calculated totals:
1. **Room Price** - Base room cost (calculated from room details)
2. **Total Charges** - Same as Room Price (room total)
3. **Total SGST** - Currently set to Rs. 0 (can be configured)
4. **Total CGST** - Currently set to Rs. 0 (can be configured)
5. **Extra Charges** - Sum of all charge amounts
6. **Total Payable** - Room Price + Extra Charges
7. **Balance** - Total Payable - Total Payments

### HTML Structure
```html
<div class="p-details">
    <!-- Charges & Payments History with Summary -->
    <div id="quickFolioHistoryDetails"></div>
</div>
```

**Note:** The title "Folio History" has been removed. All content is now dynamically rendered within a single container.

### Rendering Function
```javascript
// Render folio history details (charges and payments with summary)
function renderFolioHistory(summaryData = null) {
    const folioHistoryDiv = document.getElementById('quickFolioHistoryDetails');
    if (!folioHistoryDiv) return;
    
    let html = '<ul class="list-unstyled">';
    
    // Show charges as rows
    if (quickCharges.length > 0) {
        html += '<h6 class="mb-2 ms-3 text-danger">Charges:</h6>';
        quickCharges.forEach((charge) => {
            html += `
                <li class="d-flex align-items-center justify-content-between my-2 px-2">
                    <div>
                        <p class="mb-0"><strong>${charge.comment}</strong></p>
                        <small class="text-muted">${charge.date}</small>
                    </div>
                    <p class="mb-0 text-danger"><strong>+ Rs. ${formatAmount(charge.amount)}</strong></p>
                </li>
            `;
        });
    }
    
    // Show payments as rows
    if (quickPayments.length > 0) {
        html += '<h6 class="mb-2 ms-3 mt-3 text-success">Payments:</h6>';
        quickPayments.forEach((payment) => {
            html += `
                <li class="d-flex align-items-center justify-content-between my-2 px-2">
                    <div>
                        <p class="mb-0"><strong>${payment.type} - ${payment.method}</strong></p>
                        <small class="text-muted">${payment.comment} | ${payment.date}</small>
                    </div>
                    <p class="mb-0 text-success"><strong>- Rs. ${formatAmount(payment.amount)}</strong></p>
                </li>
            `;
        });
    }
    
    // Add summary section if data provided
    if (summaryData) {
        html += '<hr class="my-3">';
        html += `
            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                <p>Room Price</p>
                <p>Rs. ${formatAmount(summaryData.roomPrice)}</p>
            </li>
            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                <p>Total Charges</p>
                <p>Rs. ${formatAmount(summaryData.totalCharges)}</p>
            </li>
            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                <p>Total SGST</p>
                <p>Rs. 0</p>
            </li>
            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                <p>Total CGST</p>
                <p>Rs. 0</p>
            </li>
            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                <p>Extra Charges</p>
                <p>Rs. ${formatAmount(summaryData.extraCharges)}</p>
            </li>
            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                <p>Total Payable</p>
                <p>Rs. ${formatAmount(summaryData.totalPayable)}</p>
            </li>
            <li class="d-flex align-items-center justify-content-between my-2 px-2">
                <p>Balance</p>
                <p>Rs. ${formatAmount(summaryData.balance)}</p>
            </li>
        `;
    }
    
    html += '</ul>';
    folioHistoryDiv.innerHTML = html;
}
```

### Update Trigger
The `renderFolioHistory(summaryData)` function is called automatically:
- When calculations are updated via `updatePaymentCalculations()`
- When room details change (via `recalcSummary()`)
- When Payment Details tab is shown
- Summary data is passed as parameter to display totals below charges/payments

### Calculation Logic
```javascript
function updatePaymentCalculations() {
    // Calculate room total
    const nights = calculateNights();
    let roomTotal = 0;
    detailsContainer.querySelectorAll('.room-detail-item').forEach((row) => {
        const qty = parseInt(row.querySelector('.room-qty-input')?.value, 10) || 0;
        const price = parseFloat(row.querySelector('.room-price-input')?.value) || 0;
        roomTotal += qty * price * nights;
    });
    
    // Calculate charges and payments
    const totalCharges = quickCharges.reduce((sum, charge) => sum + charge.amount, 0);
    const totalPayments = quickPayments.reduce((sum, payment) => sum + payment.amount, 0);
    
    // Calculate totals
    const extraCharges = totalCharges;
    const grandTotal = roomTotal + extraCharges;
    const balance = grandTotal - totalPayments;
    
    // Update all display elements
    // ... (update Details, Folio History, and Footer sections)
}
```

---

## Balance Calculation

### Formula
```
Balance = (Room Price + Extra Charges) - Total Payments
```

### Where:
- **Room Price** = Sum of (Room Quantity × Room Price × Nights) for all room types
- **Extra Charges** = Sum of all charge amounts
- **Total Payments** = Sum of all payment amounts

### Dynamic Updates
Balance is recalculated and updated in real-time when:
- A charge is added or removed
- A payment is added or removed
- Room details are modified on Step 1
- User switches to Payment Details tab

---

## Confirm Button Logic

### Initial State
```html
<button type="button" class="theme-btn text-white ms-auto text-center text-decoration-none" 
        id="quickPaymentConfirmBtn" 
        disabled 
        style="opacity: 0.5; cursor: not-allowed;">
    <span class="label-default">Confirm</span>
    <span class="label-loading d-none">
        <span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>
        Processing...
    </span>
</button>
```

### Enable Condition
- **Rule**: At least one payment must exist
- **Check**: Performed after every payment add/remove operation

```javascript
function updateConfirmButtonState() {
    if (!quickConfirmBtn) return;
    
    if (quickPayments.length > 0) {
        quickConfirmBtn.disabled = false;
        quickConfirmBtn.style.opacity = '1';
        quickConfirmBtn.style.cursor = 'pointer';
    } else {
        quickConfirmBtn.disabled = true;
        quickConfirmBtn.style.opacity = '0.5';
        quickConfirmBtn.style.cursor = 'not-allowed';
    }
}
```

---

## Form Submission

### Data Structure
When the Confirm button is clicked, the following data is submitted:

```javascript
{
    // Booking Details (from Step 1)
    check_in_date: "2024-12-07",
    check_out_date: "2024-12-10",
    check_in_time: "14:00",
    check_out_time: "12:00",
    reservation_type: "confirmed",
    
    // Room Details
    room_details: [
        {
            room_id: 1,
            room_price_id: 5,
            rooms: 2,
            adults: 2,
            children: 0,
            price: 5000.00,
            room_number_ids: "12,15"
        }
    ],
    
    // Guest Information
    guest: {
        name: "John Doe",
        gender: "male",
        email: "john@example.com",
        mobile: "9876543210"
    },
    
    // Charges (from Step 2)
    charges: [
        {
            id: 1701234567890,
            comment: "Extra towels",
            amount: 500,
            date: "2024-12-07",          // YYYY-MM-DD (sent but not saved - created_at is used)
            display_date: "07/12/2024"   // DD/MM/YYYY for UI display
        },
        {
            id: 1701234567891,
            comment: "Room service",
            amount: 1200,
            date: "2024-12-07",          // YYYY-MM-DD (sent but not saved - created_at is used)
            display_date: "07/12/2024"   // DD/MM/YYYY for UI display
        }
    ],
    
    // Payments (from Step 2)
    payments: [
        {
            id: 1701234567892,
            type: "Cash",
            method: "Cash",
            comment: "Advance payment",
            amount: 3000,
            date: "2024-12-07",          // YYYY-MM-DD (sent but not saved - created_at is used)
            display_date: "07/12/2024"   // DD/MM/YYYY for UI display
        },
        {
            id: 1701234567893,
            type: "Online",
            method: "UPI",
            comment: "Remaining amount",
            amount: 4000,
            date: "2024-12-07",          // YYYY-MM-DD (sent but not saved - created_at is used)
            display_date: "07/12/2024"   // DD/MM/YYYY for UI display
        }
    ],
    
    // Calculated Totals (for orders table)
    room_total: 15000.00,        // Room price × nights
    total_charges: 1700.00,      // Sum of all charges
    total_payments: 7000.00,     // Sum of all payments
    grand_total: 16700.00,       // room_total + total_charges
    balance: 9700.00             // grand_total - total_payments
}
```

### Submit Implementation
```javascript
quickConfirmBtn.addEventListener('click', async function() {
    if (quickPayments.length === 0) {
        alert('Please add at least one payment before confirming.');
        return;
    }
    
    // Collect form data
    const formData = collectFormData();
    if (!formData) {
        return; // Validation failed
    }
    
    // Add charges and payments
    formData.charges = quickCharges;
    formData.payments = quickPayments;
    
    // Show loading state
    const submitLabelDefault = quickConfirmBtn.querySelector('.label-default');
    const submitLabelLoading = quickConfirmBtn.querySelector('.label-loading');
    
    try {
        if (submitLabelDefault) submitLabelDefault.classList.add('d-none');
        if (submitLabelLoading) submitLabelLoading.classList.remove('d-none');
        quickConfirmBtn.disabled = true;
        
        // Submit via API
        const response = await fetch(form.dataset.endpoint, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.content || ''
            },
            body: JSON.stringify(formData)
        });
        
        const data = await response.json();
        
        if (response.ok && data.success) {
            // Close modal
            const modal = bootstrap.Modal.getInstance(reservationModal);
            if (modal) modal.hide();
            
            // Refresh calendar
            fetchCalendarDataForCurrentView();
            
            // Show success message
            alert('Reservation created successfully!');
        } else {
            const errors = data.errors || data.message || 'An error occurred.';
            showErrors(Array.isArray(errors) ? errors : [errors]);
        }
    } catch (error) {
        console.error('Error creating reservation:', error);
        showErrors(['An error occurred while creating the reservation. Please try again.']);
    } finally {
        if (submitLabelDefault) submitLabelDefault.classList.remove('d-none');
        if (submitLabelLoading) submitLabelLoading.classList.add('d-none');
        quickConfirmBtn.disabled = false;
    }
});
```

---

## Modal Reset on Close

When the Quick Reservation modal is closed, all temporary data is cleared:

```javascript
reservationModal.addEventListener('hidden.bs.modal', () => {
    // Clear charges and payments arrays
    quickCharges.length = 0;
    quickPayments.length = 0;
    
    // Clear UI lists
    renderCharges();
    renderPayments();
    
    // Reset button state
    updateConfirmButtonState();
    
    // Clear all input fields
    if (chargeCommentInput) chargeCommentInput.value = '';
    if (chargeAmountInput) chargeAmountInput.value = '';
    if (paymentTypeSelect) paymentTypeSelect.value = '';
    if (paymentMethodSelect) {
        paymentMethodSelect.value = '';
        paymentMethodSelect.disabled = true;
        paymentMethodSelect.style.backgroundColor = '#e9ecef';
    }
    if (paymentCommentInput) paymentCommentInput.value = '';
    if (paymentAmountInput) paymentAmountInput.value = '';
    
    // Switch back to first tab
    const firstTab = document.getElementById('nav-home-tab');
    if (firstTab) {
        const tabTrigger = new bootstrap.Tab(firstTab);
        tabTrigger.show();
    }
    
    // Reset all form fields (existing reset logic)
    form.reset();
    // ... additional reset logic
});
```

---

## Backend Integration Requirements

### API Endpoint
**POST** `/api/quickReservations.store` (or as defined in form's `data-endpoint`)

### Expected Request Body
```json
{
    "check_in_date": "2024-12-07",
    "check_out_date": "2024-12-10",
    "check_in_time": "14:00",
    "check_out_time": "12:00",
    "reservation_type": "confirmed",
    "room_details": [...],
    "guest": {...},
    "charges": [...],
    "payments": [...]
}
```

### Backend Processing Steps

1. **Validate Request Data**
   - Check required fields
   - Validate date ranges
   - Verify room availability
   - Validate charge and payment amounts

2. **Create Order Record with Calculated Totals**
   ```php
   $order = Order::create([
       'check_in_date' => $request->check_in_date,
       'check_out_date' => $request->check_out_date,
       'check_in_time' => $request->check_in_time,
       'check_out_time' => $request->check_out_time,
       'reservation_type' => $request->reservation_type,
       'status' => 'confirmed',
       
       // Calculated totals from frontend
       'room_total' => $request->room_total,
       'total_charges' => $request->total_charges,
       'total_payments' => $request->total_payments,
       'grand_total' => $request->grand_total,
       'balance' => $request->balance,
       
       // OR calculate on backend for extra validation:
       // 'room_total' => $calculatedRoomTotal,
       // 'total_charges' => $request->charges->sum('amount'),
       // 'total_payments' => $request->payments->sum('amount'),
       // 'grand_total' => $calculatedRoomTotal + $request->charges->sum('amount'),
       // 'balance' => ($calculatedRoomTotal + $request->charges->sum('amount')) - $request->payments->sum('amount'),
   ]);
   ```

3. **Save Room Details**
   - Insert room booking details
   - Link to `order_id`

4. **Save Guest Information**
   - Create or update guest record
   - Link to order

5. **Save Charges**
   ```php
   foreach ($request->charges as $charge) {
       OrderPayment::create([
           'order_id' => $order->id,
           'type' => 'Charge',
           'comment' => $charge['comment'] ?? null,
           'amount' => $charge['amount'],
           // Note: 'date' field is not saved - created_at is automatically set
       ]);
   }
   ```

6. **Save Payments**
   ```php
   foreach ($request->payments as $payment) {
       OrderPayment::create([
           'order_id' => $order->id,
           'type' => 'Payment',
           'payment_type' => $payment['type'],
           'payment_method' => $payment['method'],
           'comment' => $payment['comment'] ?? null,
           'amount' => $payment['amount'],
           // Note: 'date' field is not saved - created_at is automatically set
       ]);
   }
   ```
   
   **Important**: The `date` field is sent from frontend but not saved to database. The `created_at` timestamp is used as the transaction date.

7. **Return Response**
   ```json
   {
       "success": true,
       "message": "Reservation created successfully",
       "order_id": 123,
       "grand_total": 16700.00,
       "balance": 9700.00
   }
   ```

---

### Example Backend Implementation (Laravel)

```php
public function store(Request $request)
{
    try {
        DB::beginTransaction();
        
        // 1. Create Order with calculated totals
        $order = Order::create([
            'check_in_date' => $request->check_in_date,
            'check_out_date' => $request->check_out_date,
            'check_in_time' => $request->check_in_time,
            'check_out_time' => $request->check_out_time,
            'reservation_type' => $request->reservation_type,
            'room_total' => $request->room_total,
            'total_charges' => $request->total_charges,
            'total_payments' => $request->total_payments,
            'grand_total' => $request->grand_total,
            'balance' => $request->balance,
            'status' => 'confirmed',
        ]);
        
        // 2. Save room details
        foreach ($request->room_details as $detail) {
            $order->roomDetails()->create([
                'room_id' => $detail['room_id'],
                'room_price_id' => $detail['room_price_id'],
                'rooms' => $detail['rooms'],
                'adults' => $detail['adults'],
                'children' => $detail['children'],
                'price' => $detail['price'],
                'room_number_ids' => $detail['room_number_ids'],
            ]);
        }
        
        // 3. Save guest information
        $guest = Guest::updateOrCreate(
            ['mobile' => $request->guest['mobile']],
            [
                'name' => $request->guest['name'],
                'gender' => $request->guest['gender'],
                'email' => $request->guest['email'],
            ]
        );
        $order->update(['guest_id' => $guest->id]);
        
        // 4. Save charges to order_payments
        foreach ($request->charges as $charge) {
            OrderPayment::create([
                'order_id' => $order->id,
                'type' => 'Charge',
                'comment' => $charge['comment'],
                'amount' => $charge['amount'],
                'date' => $charge['date'], // Already in YYYY-MM-DD format
            ]);
        }
        
        // 5. Save payments to order_payments
        foreach ($request->payments as $payment) {
            OrderPayment::create([
                'order_id' => $order->id,
                'type' => 'Payment',
                'payment_type' => $payment['type'],
                'payment_method' => $payment['method'],
                'comment' => $payment['comment'],
                'amount' => $payment['amount'],
                'date' => $payment['date'], // Already in YYYY-MM-DD format
            ]);
        }
        
        DB::commit();
        
        return response()->json([
            'success' => true,
            'message' => 'Reservation created successfully',
            'order_id' => $order->id,
            'grand_total' => $order->grand_total,
            'balance' => $order->balance,
        ]);
        
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json([
            'success' => false,
            'message' => 'Failed to create reservation',
            'errors' => [$e->getMessage()],
        ], 422);
    }
}
```

### Database Schema

#### `orders` Table
- `id` (primary key)
- `check_in_date` (date)
- `check_out_date` (date)
- `check_in_time` (time, nullable)
- `check_out_time` (time, nullable)
- `reservation_type` (varchar)
- `room_total` (decimal) - Room price × nights
- `total_charges` (decimal) - Sum of all extra charges
- `total_payments` (decimal) - Sum of all payments received
- `grand_total` (decimal) - room_total + total_charges
- `balance` (decimal) - grand_total - total_payments
- `status` (varchar)
- `created_at`, `updated_at`

**Calculation Formula:**
```
room_total = Sum of (room_quantity × room_price × nights)
total_charges = Sum of all charge amounts
total_payments = Sum of all payment amounts
grand_total = room_total + total_charges
balance = grand_total - total_payments
```

#### `order_payments` Table
- `id` (primary key)
- `order_id` (foreign key → orders.id)
- `type` (varchar: 'Charge' or 'Payment')
- `payment_type` (varchar: 'Cash' or 'Online', nullable - only for payments)
- `payment_method` (varchar: 'Cash', 'UPI', 'Card', 'Net Banking', nullable - only for payments)
- `amount` (decimal)
- `comment` (text, nullable) - Description or note
- `created_at`, `updated_at` - **Note**: `created_at` is used as the transaction date

#### `order_payments` Table
- `id` (primary key)
- `order_id` (foreign key → orders.id)
- `type` (enum: 'Charge', 'Payment')
- `payment_type` (varchar: 'Cash', 'Online', nullable)
- `payment_method` (varchar: 'Cash', 'UPI', 'Card', 'Net Banking', nullable)
- `comment` (text)
- `amount` (decimal)
- `date` (date)
- `created_at`, `updated_at`

---

## Testing Checklist

### Step 1 - Normal Booking
- [ ] Payment button switches to Payment Details tab
- [ ] Payment Details tab is disabled when Room Type not selected
- [ ] Payment Details tab is enabled when Room Type is selected
- [ ] Guest Name validation: Alert shown if empty when clicking Payment button
- [ ] Guest Mobile validation: Alert shown if empty when clicking Payment button
- [ ] Tab switch only happens after both Name and Mobile are filled

### Step 2 - Add Charges
- [ ] Can add charge with comment and amount
- [ ] Validation: Comment is required
- [ ] Validation: Amount must be greater than 0
- [ ] Multiple charges can be added
- [ ] Charges appear in list with delete button
- [ ] Can remove individual charges
- [ ] Extra Charges updates when charge added/removed
- [ ] Balance updates when charge added/removed
- [ ] Charges appear in Folio History details with date and amount
- [ ] Charges shown in red with + prefix in Folio History

### Step 2 - Add Payment
- [ ] Type dropdown has Cash and Online options
- [ ] When Cash selected, Method shows "Cash" and is disabled
- [ ] When Online selected, Method shows UPI/Card/Net Banking and is enabled
- [ ] Validation: Type is required
- [ ] Validation: Method is required
- [ ] Validation: Amount must be greater than 0
- [ ] Multiple payments can be added
- [ ] Payments appear in list with type, method, and delete button
- [ ] Can remove individual payments
- [ ] Total Payable updates when payment added/removed
- [ ] Balance updates when payment added/removed
- [ ] Payments appear in Folio History details with type, method, comment, and date
- [ ] Payments shown in green with - prefix in Folio History

### Charges & Payment Details Section
- [ ] "Folio History" title is not displayed
- [ ] Displays all charges under "Charges:" heading as individual rows
- [ ] Displays all payments under "Payments:" heading as individual rows
- [ ] Charges shown in red color with + prefix
- [ ] Payments shown in green color with - prefix
- [ ] Each charge row shows: comment, date, amount
- [ ] Each payment row shows: type, method, comment, date, amount
- [ ] Updates in real-time when charges/payments added or removed
- [ ] Horizontal separator (hr) displayed before summary section
- [ ] Summary section displays all 7 fields:
  - [ ] Room Price
  - [ ] Total Charges
  - [ ] Total SGST
  - [ ] Total CGST
  - [ ] Extra Charges
  - [ ] Total Payable
  - [ ] Balance
- [ ] Summary values match calculated totals
- [ ] All content uses same UI styling as before

### Confirm Button
- [ ] Initially disabled (no payments)
- [ ] Enabled when first payment is added
- [ ] Disabled again when all payments are removed
- [ ] Shows loading state during submission
- [ ] Submits all data (booking, charges, payments) together
- [ ] Closes modal on successful submission
- [ ] Refreshes calendar after submission
- [ ] Shows error messages if submission fails

### Modal Reset
- [ ] All charges cleared on modal close
- [ ] All payments cleared on modal close
- [ ] All input fields reset on modal close
- [ ] Switches back to first tab on modal close
- [ ] Confirm button disabled on modal reopen

### Calculations
- [ ] Room Price calculated correctly
- [ ] Extra Charges = Sum of all charges
- [ ] Total Payable = Room Price + Extra Charges
- [ ] Balance = Total Payable - Total Payments
- [ ] All amounts displayed in correct format (Rs. X,XXX.XX)

---

## File Modified
- `resources/views/calendar/index.blade.php`

## Implementation Date
December 6, 2024

## Version
1.1.0

---

## Recent Updates (v1.1.0)

### Date: December 6, 2025

#### Added Charges & Payments Functionality to Existing Bookings ✅
- **Feature**: Booking-pop modal now supports adding charges and payments to existing bookings
- **Functionality**:
  - **Add Charges**: Comment and Amount fields, can add multiple charges
  - **Add Payments**: Type (Cash/Online), Method, Comment, and Amount fields
  - **Folio History**: Displays all existing and new charges/payments
  - **Real-time Calculations**: Updates Grand Total, Balance, and Extra Charges dynamically
  - **Database Integration**: Saves to `order_payments` table, updates `orders` table
- **Backend Routes**:
  - `GET /order-payments/{orderId}` - Fetch existing charges/payments
  - `POST /booking/save-charges-payments` - Save new charges/payments
- **Impact**: Users can now manage charges and payments for existing bookings without creating a new reservation

---

## Previous Updates (v1.0.6)

### Date: December 6, 2025

#### Fixed Booking Modal Null Reference Error ✅
- **Issue**: `TypeError: Cannot set properties of null (setting 'textContent')`
- **Cause**: JavaScript trying to update elements that were commented out in HTML
- **Solution**: Added null checks to all `document.getElementById()` calls in `populateBookingModal` function
- **Impact**: Booking details modal now works even if some elements are commented out or missing

---

## Previous Updates (v1.0.5)

### Date: December 6, 2025

#### Removed Date Field from Database Operations ✅
- **Change**: Removed `date` field from `order_payments` table operations
- **Reason**: `created_at` timestamp is automatically saved and serves as the transaction date
- **Impact**: 
  - Frontend still maintains `date` and `display_date` for UI purposes
  - Backend only saves: `order_id`, `type`, `payment_type`, `payment_method`, `comment`, `amount`
  - `created_at` and `updated_at` are automatically managed by Laravel
- **Benefit**: Simpler database structure, no need for additional date column

---

## Previous Updates (v1.0.4)

### Date: December 6, 2024

#### Grand Total and Balance Calculation for Orders Table ✅
- **Feature**: Automatically calculate and send totals to backend for orders table
- **Implementation**: Before submission, calculate:
  - `room_total`: Room price × nights
  - `total_charges`: Sum of all charge amounts
  - `total_payments`: Sum of all payment amounts
  - `grand_total`: room_total + total_charges
  - `balance`: grand_total - total_payments
- **Benefit**: Backend receives pre-calculated totals for easy storage in orders table
- **Formula**:
  ```
  Balance = (Room Total + Total Charges) - Total Payments
  ```

---

## Previous Updates (v1.0.3)

### Date: December 6, 2024

#### Date Format Fix for Database Compatibility ✅
- **Issue**: Charges and payments dates were stored in DD/MM/YYYY format which wasn't database-friendly
- **Fix**: Changed date storage to use YYYY-MM-DD format (ISO standard)
- **Implementation**:
  - `date` field: Stores in YYYY-MM-DD format for database compatibility
  - `display_date` field: Stores in DD/MM/YYYY format for UI display
- **Benefit**: Backend can now properly parse and save dates to the `order_payments` table

---

## Previous Updates (v1.0.2)

### Date: December 6, 2024

#### 1. Charges & Payment Details Section Redesign ✅
- **Feature**: Restructured the display to show charges and payments as individual rows, followed by summary totals
- **Changes**:
  - **Title Removed**: "Folio History" heading removed
  - **Transaction Rows**: Each charge and payment displayed as a separate row with details
  - **Summary Integration**: Summary totals (Room Price, Total Charges, SGST, CGST, Extra Charges, Total Payable, Balance) now appear after transactions with a separator
  - **Same UI Area**: All content displayed in the same location using consistent styling
- **Display Format**:
  - **Charges**: Individual rows showing comment, date, amount (red with + prefix)
  - **Payments**: Individual rows showing type, method, comment, date, amount (green with - prefix)
  - **Summary**: Displayed after a horizontal line separator

---

## Previous Updates (v1.0.1)

### Date: December 6, 2024

#### 1. Guest Information Validation ✅
- **Feature**: Added mandatory validation for Guest Information when clicking Payment button
- **Fields Required**:
  - Guest Name (mandatory)
  - Guest Mobile (mandatory)
- **Behavior**: 
  - User cannot proceed to Payment Details tab without filling these fields
  - Shows focused alert messages for each missing field
  - Automatically focuses on the empty field

#### 2. Folio History Enhancement ✅
- **Feature**: Payment and charges history displayed inside section
- **Display Format**:
  - **Charges Section**: Shows all charges with comment, date, and amount in red (+ prefix)
  - **Payments Section**: Shows all payments with type, method, comment, date, and amount in green (- prefix)
- **Real-time Updates**: History updates automatically when charges/payments are added or removed
- **Visual Distinction**: 
  - Charges in red color to indicate additions
  - Payments in green color to indicate deductions

---

## Notes
- All monetary calculations use JavaScript's native `parseFloat()` for precision
- **Date Handling**: 
  - Database storage: `toISOString().split('T')[0]` for YYYY-MM-DD format
  - UI display: `toLocaleDateString('en-GB')` for DD/MM/YYYY format
  - Two separate fields maintain compatibility: `date` (database) and `display_date` (UI)
- Bootstrap 5 Tab API is used for tab switching
- All form submissions use `fetch()` API with JSON payload
- Error handling includes both client-side validation and server response handling
- Guest validation uses browser's native `alert()` for immediate user feedback
- Folio History automatically scrolls/updates when new transactions are added

---

# Booking Management - Existing Bookings

## Overview
The booking-pop modal allows users to view and manage existing bookings, including adding new charges and payments.

---

## Features

### 1. View Existing Booking Details
- **Booking Information**: Guest name, folio number, dates, room details
- **Payment History**: All existing charges and payments from the database
- **Summary Calculations**: Current totals, paid amounts, and balance

### 2. Add Charges

#### Fields
- **Comment** (Text) - Description of the charge
- **Amount** (Number) - Charge amount

#### Functionality
- Click "Add Charge" button to add a new charge
- Multiple charges can be added
- New charges appear in a list with:
  - Comment
  - Date (current date)
  - Amount
  - Remove button (X)
- Charges are marked as "new" until saved

#### Validation
- Comment is required
- Amount must be greater than 0

### 3. Add Payments

#### Fields
- **Type** (Dropdown) - Cash or Online
- **Method** (Dropdown) - Conditional based on Type
  - If Type = Cash → Method = "Cash" (auto-selected, disabled)
  - If Type = Online → Method = UPI / Card / Net Banking
- **Comment** (Text) - Optional payment note
- **Amount** (Number) - Payment amount

#### Functionality
- Click "Add Payment" button to add a new payment
- Multiple payments can be added
- New payments appear in a list with:
  - Comment
  - Type and Method
  - Date (current date)
  - Amount
  - Remove button (X)
- Payments are marked as "new" until saved

#### Validation
- Type is required
- Method is required
- Amount must be greater than 0

### 4. Folio History Display

#### Structure
The Folio History section displays all charges and payments in chronological order:

**Charges Section:**
- Shows all existing charges from database (white background)
- Shows newly added charges (light gray background)
- Format: Comment | Date | Amount (in red)

**Payments Section:**
- Shows all existing payments from database (white background)
- Shows newly added payments (light gray background)
- Format: Comment | Type - Method | Date | Amount (in green)

#### Visual Indicators
- **Existing entries**: White background (already in database)
- **New entries**: Light gray background (pending save)
- **Charges**: Red text for amounts
- **Payments**: Green text for amounts

### 5. Real-time Calculations

The modal automatically recalculates totals when charges or payments are added:

```javascript
// Calculation Logic
New Grand Total = Original Grand Total + Total New Charges
New Balance = New Grand Total - Total New Payments
Extra Charges = Total New Charges
```

**Updated Fields:**
- Extra Charges (displays new charges only)
- Total Payable (updated grand total)
- Balance (updated balance)
- Footer Total, Paid, and Balance

---

## Backend Integration

### Routes

#### GET /order-payments/{orderId}
Fetches existing charges and payments for a booking.

**Response:**
```json
{
  "success": true,
  "payments": [
    {
      "id": 1,
      "order_id": 5,
      "type": "Charge",
      "comment": "Room Service",
      "amount": "150.00",
      "created_at": "2025-12-06 10:30:00"
    },
    {
      "id": 2,
      "order_id": 5,
      "type": "Payment",
      "payment_type": "Cash",
      "payment_method": "Cash",
      "comment": "Advance payment",
      "amount": "5000.00",
      "created_at": "2025-12-06 11:00:00"
    }
  ]
}
```

#### POST /booking/save-charges-payments
Saves new charges and payments to the database.

**Request:**
```json
{
  "order_id": 5,
  "charges": [
    {
      "comment": "Tea",
      "amount": 100
    }
  ],
  "payments": [
    {
      "type": "Cash",
      "method": "Cash",
      "comment": "Payment",
      "amount": 1000
    }
  ]
}
```

**Response:**
```json
{
  "success": true,
  "message": "Charges and payments saved successfully"
}
```

### Controller Methods

#### `getOrderPayments($orderId)`
- Fetches all records from `order_payments` table for the given order_id
- Returns JSON response with payments array

#### `saveBookingChargesPayments(Request $request)`
- Validates incoming data
- Saves charges to `order_payments` table with `type = 'Charge'`
- Saves payments to `order_payments` table with `type = 'Payment'`
- Updates `orders` table:
  - Adds charge amounts to `grand_total`
  - Subtracts payment amounts from `balance`
- Uses database transactions for data integrity

---

## Database Structure

### order_payments Table
```sql
- id (primary key)
- order_id (foreign key to orders.id)
- type ('Charge' or 'Payment')
- payment_type ('Cash', 'Online', or NULL for charges)
- payment_method ('Cash', 'UPI', 'Card', 'Net Banking', or NULL for charges)
- comment (varchar, nullable)
- amount (decimal)
- created_at (timestamp) - Used as transaction date
- updated_at (timestamp)
```

### orders Table Updates
When charges/payments are saved:
- `grand_total` = original grand_total + sum of new charges
- `balance` = new grand_total - sum of new payments

---

## JavaScript Functions

### Core Functions

#### `loadBookingPaymentHistory(orderId)`
- Fetches existing charges and payments from database
- Separates them into `bookingCharges` and `bookingPayments` arrays
- Marks entries as `existing: true`
- Renders all lists and updates calculations

#### `renderBookingChargesList()`
- Displays new charges in the "Add Charges" section
- Shows comment, date, amount, and remove button

#### `renderBookingPaymentsList()`
- Displays new payments in the "Add Payments" section
- Shows comment, type-method, date, amount, and remove button

#### `renderBookingFolioHistory()`
- Displays all charges and payments (existing + new) in Folio History
- Groups by type (Charges first, then Payments)
- Different background colors for existing vs. new entries

#### `recalcBookingSummary()`
- Calculates totals based on original booking data + new entries
- Updates Extra Charges, Total Payable, and Balance
- Updates footer totals

#### `saveBookingChargesPayments()`
- Submits new charges and payments to backend
- Shows success/error messages
- Reloads payment history after successful save

### Event Handlers

```javascript
// Payment Type Change
document.getElementById('bookingPaymentType').addEventListener('change', function() {
    // Updates method dropdown based on selected type
});

// Add Charge Button
document.getElementById('bookingChargeAddBtn').addEventListener('click', function() {
    // Validates and adds charge to array
});

// Add Payment Button
document.getElementById('bookingPaymentAddBtn').addEventListener('click', function() {
    // Validates and adds payment to array
});

// Remove Functions
window.removeBookingCharge(index) // Removes charge from array
window.removeBookingPayment(index) // Removes payment from array
```

---

## User Workflow

### Step 1: Open Existing Booking
1. User clicks on a booking in the calendar
2. Booking-pop modal opens
3. System loads booking details from database
4. System fetches existing charges and payments
5. Folio History displays all existing entries

### Step 2: Add New Charges (Optional)
1. User enters comment in "Comment" field
2. User enters amount in "Amount" field
3. User clicks "Add Charge" button
4. Charge appears in the charges list (gray background)
5. Folio History updates to show new charge
6. Extra Charges and totals recalculate

### Step 3: Add New Payments (Optional)
1. User selects payment type (Cash/Online)
2. Method dropdown updates automatically
3. User selects payment method
4. User enters optional comment
5. User enters payment amount
6. User clicks "Add Payment" button
7. Payment appears in the payments list (gray background)
8. Folio History updates to show new payment
9. Balance and totals recalculate

### Step 4: Review and Save
1. User reviews all entries in Folio History
2. User can remove any new entries using X button
3. User clicks "Save Changes" button
4. System validates data
5. System saves to database
6. Success message displays
7. Modal refreshes to show all entries as "existing"
8. Totals are permanently updated in database

---

## Error Handling

### Frontend Validation
- Missing comment for charges
- Missing type or method for payments
- Invalid or zero amounts
- All show user-friendly alert messages

### Backend Validation
- Order ID validation (must exist in orders table)
- Array validation for charges and payments
- Field-level validation (required fields, data types, min values)
- Returns detailed error messages on failure

### Database Errors
- Transaction rollback on any error
- Error logged to Laravel logs
- User-friendly error message returned to frontend

---

## Testing Checklist

### Viewing Existing Bookings
- [ ] Click on existing booking opens modal
- [ ] Booking details display correctly
- [ ] Existing charges show in Folio History (if any)
- [ ] Existing payments show in Folio History (if any)
- [ ] Totals match database values

### Adding Charges
- [ ] Can add charge with comment and amount
- [ ] Charge appears in list with correct format
- [ ] Can add multiple charges
- [ ] Can remove newly added charges
- [ ] Extra Charges total updates correctly
- [ ] Grand Total updates correctly
- [ ] Folio History shows new charges (gray background)

### Adding Payments
- [ ] Type dropdown works (Cash/Online)
- [ ] Method dropdown updates based on type
- [ ] Cash type auto-selects Cash method
- [ ] Online type shows UPI/Card/Net Banking options
- [ ] Can add payment with all details
- [ ] Payment appears in list with correct format
- [ ] Can add multiple payments
- [ ] Can remove newly added payments
- [ ] Balance updates correctly
- [ ] Folio History shows new payments (gray background)

### Saving Data
- [ ] "Save Changes" button submits data
- [ ] Data saves to order_payments table
- [ ] Orders table updates (grand_total and balance)
- [ ] Success message displays
- [ ] Modal refreshes with updated data
- [ ] All entries now show as existing (white background)
- [ ] Reopen modal shows correct totals

### Error Cases
- [ ] Empty comment for charge shows error
- [ ] Zero amount shows error
- [ ] Missing payment type shows error
- [ ] Missing payment method shows error
- [ ] Database errors show user-friendly message

---

## Notes

### Date Handling
- `created_at` timestamp is used as the transaction date
- Display format: DD/MM/YYYY (using `toLocaleDateString('en-GB')`)
- No separate date column needed in database

### Calculation Logic
- Only NEW charges and payments affect the calculations
- Existing entries are already included in the original grand_total
- This prevents double-counting when reopening the modal

### Visual Design
- Consistent with Quick Reservation modal styling
- Bootstrap classes for responsive layout
- Color coding for charges (red) vs payments (green)
- Background colors distinguish existing vs new entries

### Performance
- Fetch call on modal open (lightweight query)
- Real-time updates on client side (no API calls until save)
- Single POST request saves all new entries together
- Database transaction ensures data integrity

