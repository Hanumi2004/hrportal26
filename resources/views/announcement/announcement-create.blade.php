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
                                        <li class="breadcrumb-item"><a href="{{ route('admin.dashboard') }}">Dashboard</a>
                                        </li>
                                        <li class="breadcrumb-item"><a
                                                href="{{ route('announcement.index.admin') }}">Announcement</a></li>
                                        <li class="breadcrumb-item active" aria-current="page">New Announcement</li>
                                    </ol>
                                </nav>
                                <h3 class="page-title"><br>New Announcement</h3>
                                <p class="text-muted">Create new announcement.</p>
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

                    <form action="{{ route('announcement.store') }}" method="POST" novalidate>
                        @csrf

                        <div class="mb-3">
                            <label for="title" class="form-label">Title<span class="text-danger">*</span></label>
                            <input type="text" id="title" name="title" class="form-control"
                                placeholder="Enter announcement title" value="{{ old('title') }}" required>
                            @error('title')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description<span class="text-danger">*</span></label>
                            <textarea id="description" name="description" rows="3" class="form-control"
                                placeholder="Describe the announcement" required>{{ old('description') }}</textarea>
                            @error('description')
                                <div class="text-danger small">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="row g-3 mb-3">
                            <div class="col-md-6">
                                <label for="category" class="form-label">Category<span class="text-danger">*</span></label>
                                <select id="category"name="category" class="form-select" required>
                                    <option value="" disabled {{ old('category') ? '' : 'selected' }}>
                                        Select Category</option>
                                    <option value="general" {{ old('category') === 'general' ? 'selected' : '' }}>
                                        General</option>
                                    <option value="policy" {{ old('category') === 'policy' ? 'selected' : '' }}>
                                        Policy</option>
                                    <option value="system" {{ old('category') === 'system' ? 'selected' : '' }}>
                                        System</option>
                                    <option value="other" {{ old('category') === 'other' ? 'selected' : '' }}>
                                        Other</option>
                                </select>
                                @error('category')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="priority" class="form-label">Priority<span class="text-danger">*</span></label>
                                <select id="priority"name="priority" class="form-select" required>
                                    <option value="" disabled {{ old('priority') ? '' : 'selected' }}>
                                        Select Priority</option>
                                    <option value="low" {{ old('priority') === 'low' ? 'selected' : '' }}>
                                        Low</option>
                                    <option value="medium" {{ old('priority') === 'medium' ? 'selected' : '' }}>
                                        Medium</option>
                                    <option value="high" {{ old('priority') === 'high' ? 'selected' : '' }}>
                                        High</option>
                                </select>
                                @error('priority')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label for="expires_date" class="form-label">Expires Date</label>
                                <input type="date" id="expires_date" name="expires_date" class="form-control"
                                    value="{{ old('expires_date') }}">
                                @error('expires_date')
                                    <div class="text-danger small">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="d-flex justify-content-end">
                                <a href="{{ route('announcement.index.admin') }}" class="btn btn-secondary me-2">
                                    Cancel
                                </a>
                                <button type="submit" class="btn btn-primary">
                                    Create Announcement
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
@endsection
