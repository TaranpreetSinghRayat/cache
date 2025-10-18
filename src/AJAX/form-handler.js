/**
 * Forms Library AJAX Handler
 * Handles form submission, validation, and AJAX requests
 */

class FormHandler {
    constructor(formSelector, options = {}) {
        this.form = document.querySelector(formSelector);
        if (!this.form) {
            console.error(`Form not found: ${formSelector}`);
            return;
        }

        this.options = {
            submitUrl: options.submitUrl || this.form.getAttribute('action'),
            method: options.method || this.form.getAttribute('method') || 'POST',
            onSuccess: options.onSuccess || (() => {}),
            onError: options.onError || (() => {}),
            onValidationError: options.onValidationError || (() => {}),
            validateOnChange: options.validateOnChange !== false,
            validateOnBlur: options.validateOnBlur !== false,
            showLoadingState: options.showLoadingState !== false,
            loadingClass: options.loadingClass || 'is-loading',
            errorClass: options.errorClass || 'has-error',
            successClass: options.successClass || 'has-success',
            ...options
        };

        this.init();
    }

    init() {
        this.attachFormListener();
        if (this.options.validateOnChange) {
            this.attachFieldListeners();
        }
    }

    attachFormListener() {
        this.form.addEventListener('submit', (e) => this.handleSubmit(e));
    }

    attachFieldListeners() {
        const fields = this.form.querySelectorAll('input, textarea, select');
        fields.forEach(field => {
            if (this.options.validateOnBlur) {
                field.addEventListener('blur', () => this.validateField(field));
            }
            if (this.options.validateOnChange) {
                field.addEventListener('change', () => this.validateField(field));
            }
        });
    }

    handleSubmit(e) {
        e.preventDefault();
        this.submit();
    }

    async submit() {
        const formData = this.getFormData();
        
        if (this.options.showLoadingState) {
            this.setLoadingState(true);
        }

        try {
            const response = await this.sendRequest(formData);
            
            if (response.success) {
                this.clearErrors();
                this.options.onSuccess(response);
            } else {
                if (response.errors) {
                    this.displayErrors(response.errors);
                    this.options.onValidationError(response);
                }
                this.options.onError(response);
            }
        } catch (error) {
            console.error('Form submission error:', error);
            this.options.onError({ error: error.message });
        } finally {
            if (this.options.showLoadingState) {
                this.setLoadingState(false);
            }
        }
    }

    async validateField(field) {
        const fieldName = field.name;
        const fieldValue = field.value;
        const fieldGroup = field.closest('.form-group');

        try {
            const response = await this.sendRequest({ [fieldName]: fieldValue }, true);
            
            if (response.errors && response.errors[fieldName]) {
                this.displayFieldError(fieldGroup, response.errors[fieldName]);
            } else {
                this.clearFieldError(fieldGroup);
            }
        } catch (error) {
            console.error('Field validation error:', error);
        }
    }

    getFormData() {
        const formData = new FormData(this.form);
        const data = {};

        for (let [key, value] of formData.entries()) {
            if (key.endsWith('[]')) {
                const fieldName = key.slice(0, -2);
                if (!data[fieldName]) {
                    data[fieldName] = [];
                }
                data[fieldName].push(value);
            } else {
                data[key] = value;
            }
        }

        return data;
    }

    async sendRequest(data, validateOnly = false) {
        const url = this.options.submitUrl;
        const method = this.options.method.toUpperCase();

        const options = {
            method: method,
            headers: {
                'Content-Type': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
            },
            body: JSON.stringify(data),
        };

        if (validateOnly) {
            options.headers['X-Validate-Only'] = 'true';
        }

        const response = await fetch(url, options);
        return await response.json();
    }

    displayErrors(errors) {
        for (const [fieldName, errorMessages] of Object.entries(errors)) {
            const field = this.form.querySelector(`[name="${fieldName}"]`);
            if (field) {
                const fieldGroup = field.closest('.form-group');
                if (fieldGroup) {
                    const errorArray = Array.isArray(errorMessages) ? errorMessages : [errorMessages];
                    this.displayFieldError(fieldGroup, errorArray[0]);
                }
            }
        }
    }

    displayFieldError(fieldGroup, error) {
        fieldGroup.classList.add(this.options.errorClass);
        fieldGroup.classList.remove(this.options.successClass);

        let errorElement = fieldGroup.querySelector('.error-message');
        if (!errorElement) {
            errorElement = document.createElement('div');
            errorElement.className = 'error-message';
            fieldGroup.appendChild(errorElement);
        }
        errorElement.textContent = error;
    }

    clearFieldError(fieldGroup) {
        fieldGroup.classList.remove(this.options.errorClass);
        fieldGroup.classList.add(this.options.successClass);

        const errorElement = fieldGroup.querySelector('.error-message');
        if (errorElement) {
            errorElement.remove();
        }
    }

    clearErrors() {
        const fieldGroups = this.form.querySelectorAll('.form-group');
        fieldGroups.forEach(group => {
            group.classList.remove(this.options.errorClass);
            group.classList.remove(this.options.successClass);
            const errorElement = group.querySelector('.error-message');
            if (errorElement) {
                errorElement.remove();
            }
        });
    }

    setLoadingState(isLoading) {
        const submitButton = this.form.querySelector('button[type="submit"]');
        if (submitButton) {
            if (isLoading) {
                submitButton.classList.add(this.options.loadingClass);
                submitButton.disabled = true;
            } else {
                submitButton.classList.remove(this.options.loadingClass);
                submitButton.disabled = false;
            }
        }
    }

    reset() {
        this.form.reset();
        this.clearErrors();
    }

    setFieldValue(fieldName, value) {
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        if (field) {
            field.value = value;
        }
    }

    getFieldValue(fieldName) {
        const field = this.form.querySelector(`[name="${fieldName}"]`);
        return field ? field.value : null;
    }
}

// Export for use in modules
if (typeof module !== 'undefined' && module.exports) {
    module.exports = FormHandler;
}

