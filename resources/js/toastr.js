import toastr from 'toastr';
import 'toastr/build/toastr.min.css';

export function useToastr(){
    toastr.options.closeMethod = 'fadeOut';
    toastr.options.closeDuration = 300;
    toastr.options.closeEasing = 'swing';
    toastr.options.positionClass = 'toast-bottom-right';
    return toastr;
}