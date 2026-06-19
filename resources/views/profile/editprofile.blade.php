@extends('layouts.master')

@section('content')
    <div class="content container-fluid">

        <div class="page-header">
            <div class="row">
                <div class="col-sm-12">
                    <div class="page-sub-header">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <nav aria-label="breadcrumb">
                                    <ol class="breadcrumb mb-0">
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Home</a></li>
                                        <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Profile</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">Edit Profile</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>Edit Personal Details</h3>
                                <p class="text-muted">Update your personal details below.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-12 col-md-12">
            <div class="card">
                <div class="card-body justify-content-between">
                    {{-- makes content flexible row-pushes text left, icon right --}}

                    <form action="{{ route('profile.updatePersonal', $employee->employee_id) }}" method="POST"
                        enctype="multipart/form-data" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="mb-3">
                            {{-- mb-3 = margin-bottom 1rem
                            mt-3 = margin-top 1rem
                            g-3 = gap 1rem --}}
                            <label for="full_name" class="form-label">Full Name <span class="text-danger">*</span></label>
                            <input type="text" id="full_name" name="full_name" class="form-control"
                                placeholder="Enter your full name" value="{{ old('full_name', $employee->full_name) }}"
                                required>
                            {{-- Using for="event_name" links the label to the input’s id, so clicking the label focuses the input. --}}
                            @error('full_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="email" class="form-label">Email Address <span
                                        class="text-danger">*</span></label>
                                <input type="email" id="email" name="email" class="form-control"
                                    placeholder="Enter your email address" value="{{ old('email', $employee->email) }}"
                                    required>
                                @error('email')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="phone_number" class="form-label">Phone Number <span
                                        class="text-danger">*</span></label>
                                <input type="text" id="phone_number" name="phone_number" class="form-control"
                                    placeholder="Enter your phone number"
                                    value="{{ old('phone_number', $employee->phone_number) }}" required>
                                @error('phone_number')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="address" class="form-label">Address <span class="text-danger">*</span></label>
                            <textarea id="address" name="address" class="form-control" rows="4" placeholder="Add your address" required>{{ old('address', $employee->address) }}</textarea>
                            @error('address')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <div class="mb-3">
                            <label for="ic_number" class="form-label">IC Number <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="ic_number" name="ic_number" class="form-control"
                                placeholder="Enter IC Number" value="{{ old('ic_number', $employee->ic_number) }}"
                                required>
                            @error('ic_number')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">

                            <div class="col-md-6">
                                <label for="gender" class="form-label">Gender <span class="text-danger">*</span></label>
                                <select id="gender" name="gender" class="form-select" required>
                                    <option value="" disabled {{ old('gender') ? '' : 'selected' }}>Select gender
                                    </option>
                                    @php
                                        $genders = ['male', 'female'];
                                    @endphp
                                    @foreach ($genders as $gen)
                                        <option value="{{ $gen }}"
                                            {{ old('gender', $employee->gender) === $gen ? 'selected' : '' }}>
                                            {{ ucfirst($gen) }}</option>
                                    @endforeach
                                </select>
                                @error('gender')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="birthday" class="form-label">Birthday <span class="text-danger">*</span></label>
                                <input type="date" id="birthday" name="birthday" class="form-control"
                                    value="{{ old('birthday', $employee->birthday?->format('Y-m-d')) }}" required>
                                @error('birthday')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="marital_status" class="form-label">Marital Status <span
                                        class="text-danger">*</span></label>
                                <select id="marital_status" name="marital_status" class="form-select" required>
                                    <option value="" disabled {{ old('marital_status') ? '' : 'selected' }}>Select
                                        status
                                    </option>
                                    @php
                                        $marital_statuses = ['single', 'married', 'divorced', 'widowed'];
                                    @endphp
                                    @foreach ($marital_statuses as $status)
                                        <option value="{{ $status }}"
                                            {{ old('marital_status', $employee->marital_status) === $status ? 'selected' : '' }}>
                                            {{ ucfirst($status) }}</option>
                                    @endforeach
                                </select>
                                @error('marital_status')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="nationality" class="form-label">Nationality <span
                                        class="text-danger">*</span></label>
                                <select id="nationality" name="nationality" class="form-select" required>
                                    @php
                                        $nationalities = ['malaysian', 'singaporean', 'indonesian', 'other'];
                                    @endphp
                                    @foreach ($nationalities as $nationality)
                                        <option value="{{ $nationality }}"
                                            {{ old('nationality', $employee->nationality) === $nationality ? 'selected' : '' }}>
                                            {{ ucfirst($nationality) }}</option>
                                    @endforeach
                                </select>
                                @error('nationality')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <hr class="my-3">

                        <div class="mb-3">
                            <label for="emergency_contact_name" class="form-label">Emergency Contact Name <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="emergency_contact_name" name="emergency_contact_name"
                                class="form-control" placeholder="Enter your emergency contact's name"
                                value="{{ old('emergency_contact_name', $employee->emergency_contact_name) }}" required>
                            @error('emergency_contact_name')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="emergency_contact_number" class="form-label">Emergency Contact Number <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="emergency_contact_number" name="emergency_contact_number"
                                class="form-control" placeholder="Enter your emergency contact's number"
                                value="{{ old('emergency_contact_number', $employee->emergency_contact_number) }}"
                                required>
                            @error('emergency_contact_number')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="emergency_contact_relationship" class="form-label">Emergency Contact Relationship
                                <span class="text-danger">*</span></label>
                            <input type="text" id="emergency_contact_relationship"
                                name="emergency_contact_relationship" class="form-control"
                                placeholder="Enter your emergency contact's relationship"
                                value="{{ old('emergency_contact_relationship', $employee->emergency_contact_relationship) }}"
                                required>
                            @error('emergency_contact_relationship')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>
                        
                        <hr class="my-3">

                        <div class="mb-3">
                            <label for="highest_education_level" class="form-label">Highest Education Level <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="highest_education_level" name="highest_education_level" class="form-control"
                                placeholder="Enter your highest education level"
                                value="{{ old('highest_education_level', $employee->highest_education_level) }}" required>
                            @error('highest_education_level')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="highest_education_institution" class="form-label">Highest Education Institution <span
                                    class="text-danger">*</span></label>
                            <input type="text" id="highest_education_institution" name="highest_education_institution"
                                class="form-control" placeholder="Enter your highest education institution"
                                value="{{ old('highest_education_institution', $employee->highest_education_institution) }}" required>
                            @error('highest_education_institution')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="graduation_year" class="form-label">Graduation Year <span
                                    class="text-danger">*</span></label>
                            <input type="number" id="graduation_year" name="graduation_year" class="form-control"
                                placeholder="Enter your graduation year"
                                value="{{ old('graduation_year', $employee->graduation_year) }}" required>
                            @error('graduation_year')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="d-flex justify-content-end">
                            <a href="{{ route('profile.show', $employee->employee_id) }}" class="btn btn-secondary me-2">
                                Cancel
                            </a>
                            {{-- later add if/else for employee/admin --}}
                            <button type="submit" class="btn btn-primary">
                                Update Profile
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
