     <script>
         window.addEventListener('livewire:load', () => {
             initializeSelect2();
         });

         function initializeSelect2() {
             $('.select2').each(function() {
                 let modelName = $(this).data('model');
                 $(this).select2({
                     theme: 'bootstrap4',
                     width: $(this).data('width') ? $(this).data('width') : $(this).hasClass('w-100') ?
                         '100%' : 'style',
                     placeholder: $(this).data('placeholder') ? $(this).data('placeholder') : 'Select',
                     allowClear: Boolean($(this).data('allow-clear')),
                 }).off('change').on('change', function(e) {
                     if (modelName) {
                         @this.set(modelName, $(this).val());
                     }
                 });
             });
         }

         window.addEventListener('livewire:update', () => {
             $('.select2').select2('destroy'); //destroy the previous instances of select2
             initializeSelect2();
         });
     </script>
