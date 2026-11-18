<div class="space-y-3 text-left">
    <div>
        <label for="cancellation_reason" class="mb-1.5 block text-xs font-bold uppercase tracking-wide text-slate-700">
            Reason for cancellation
        </label>
        <select
            id="cancellation_reason"
            name="cancellation_reason"
            required
            class="w-full rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-700 focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20"
        >
            <option value="">Choose a reason</option>
            @foreach (\App\Models\CourseEnrollmentRequest::CANCELLATION_REASONS as $reason => $label)
                <option value="{{ $reason }}" {{ old('cancellation_reason') === $reason ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('cancellation_reason')
            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
        @enderror
    </div>
    <div>
        <label for="cancellation_details" class="mb-1.5 block text-xs font-semibold text-slate-700">
            Additional details <span class="font-normal text-slate-400">(optional)</span>
        </label>
        <textarea
            id="cancellation_details"
            name="cancellation_details"
            rows="3"
            maxlength="1000"
            class="w-full resize-y rounded-xl border border-slate-300 bg-white px-3.5 py-2.5 text-sm text-slate-700 placeholder-slate-400 focus:border-ncc-maroon focus:outline-none focus:ring-2 focus:ring-ncc-maroon/20"
            placeholder="Share any details that may help with the review."
        >{{ old('cancellation_details') }}</textarea>
        @error('cancellation_details')
            <p class="mt-1 text-xs text-red-700">{{ $message }}</p>
        @enderror
    </div>
</div>
