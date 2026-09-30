<div class="grid gap-5 sm:grid-cols-2">
    <div class="sm:col-span-2">
        <label for="name" class="mb-1.5 block text-sm font-medium text-[#35443e]">Supplier name</label>
        <input id="name" type="text" name="name" value="{{ old('name', $supplier?->name) }}" required maxlength="255" class="w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none placeholder:text-[#929b97] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('name') aria-invalid="true" aria-describedby="supplier-name-error" @enderror>
        @error('name') <p id="supplier-name-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="contact_person" class="mb-1.5 block text-sm font-medium text-[#35443e]">Contact person <span class="font-normal text-[#818c88]">(optional)</span></label>
        <input id="contact_person" type="text" name="contact_person" value="{{ old('contact_person', $supplier?->contact_person) }}" maxlength="255" class="w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none placeholder:text-[#929b97] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('contact_person') aria-invalid="true" aria-describedby="contact-person-error" @enderror>
        @error('contact_person') <p id="contact-person-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="email" class="mb-1.5 block text-sm font-medium text-[#35443e]">Email <span class="font-normal text-[#818c88]">(optional)</span></label>
        <input id="email" type="email" name="email" value="{{ old('email', $supplier?->email) }}" maxlength="255" autocomplete="email" class="w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none placeholder:text-[#929b97] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('email') aria-invalid="true" aria-describedby="supplier-email-error" @enderror>
        @error('email') <p id="supplier-email-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div>
        <label for="phone" class="mb-1.5 block text-sm font-medium text-[#35443e]">Phone <span class="font-normal text-[#818c88]">(optional)</span></label>
        <input id="phone" type="tel" name="phone" value="{{ old('phone', $supplier?->phone) }}" maxlength="20" autocomplete="tel" class="w-full rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none placeholder:text-[#929b97] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('phone') aria-invalid="true" aria-describedby="phone-error" @enderror>
        @error('phone') <p id="phone-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>

    <div class="sm:col-span-2">
        <label for="address" class="mb-1.5 block text-sm font-medium text-[#35443e]">Address <span class="font-normal text-[#818c88]">(optional)</span></label>
        <textarea id="address" name="address" rows="4" class="w-full resize-y rounded-md border border-[#d8e0dc] bg-white px-3 py-2.5 text-sm text-[#26332f] outline-none placeholder:text-[#929b97] focus:border-[#237b65] focus:ring-2 focus:ring-[#237b65]/15" @error('address') aria-invalid="true" aria-describedby="address-error" @enderror>{{ old('address', $supplier?->address) }}</textarea>
        @error('address') <p id="address-error" class="mb-0 mt-1.5 text-sm text-red-700">{{ $message }}</p> @enderror
    </div>
</div>