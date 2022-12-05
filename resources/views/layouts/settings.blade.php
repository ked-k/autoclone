  <!-- Right Sidebar -->
  <div class="end-bar">

      <div class="rightbar-title">
          <a href="javascript:void(0);" class="end-bar-toggle float-end">
              <i class="dripicons-cross noti-icon"></i>
          </a>
          <h5 class="m-0">Settings</h5>
      </div>

      <div class="rightbar-content h-100" data-simplebar="">

          <div class="p-3">
              <div class="alert alert-info" role="alert">
                  <strong>Customize </strong> the overall color scheme, sidebar menu, etc.
              </div>

              <!-- Settings -->
              <h5 class="mt-3">Color Scheme</h5>
              <hr class="mt-1">

              <form method="POST" action="{{ route('settings.update', Auth::id()) }}">
                  @csrf
                  @method('PUT')
                  <div class="mt-3">
                      <div class="form-check form-radio-success">
                          <input type="radio" id="light-mode-check" name="color_scheme"
                              class="form-check-input color-scheme" value="false"
                              onclick="event.preventDefault(); this.closest('form').submit();">
                          <label class="form-check-label" for="light-mode-check">Light Mode</label>
                      </div>
                      <div class="form-check form-radio-success mt-2">
                          <input type="radio" id="dark-mode-check" name="color_scheme"
                              class="form-check-input color-scheme" value="true"
                              onclick="event.preventDefault(); this.closest('form').submit();">
                          <label class="form-check-label" for="dark-mode-check">Dark Mode</label>
                      </div>
                  </div>
              </form>

              <!-- Left Sidebar-->
              <h5 class="mt-4">Left Sidebar</h5>
              <hr class="mt-1">


              <form method="POST" action="{{ route('settings.update', Auth::id()) }}">
                  @csrf
                  @method('PUT')
                  <div class="mt-3">
                      <div class="form-check form-radio-success">
                          <input type="radio" id="default-check" name="left_sidebar_theme"
                              class="form-check-input leftsidebar" value="default"
                              onclick="event.preventDefault(); this.closest('form').submit();">
                          <label class="form-check-label" for="default-check">Default</label>
                      </div>
                      <div class="form-check form-radio-success mt-1">
                          <input type="radio" id="light-check" name="left_sidebar_theme" class="form-check-input"
                              value="light" onclick="event.preventDefault(); this.closest('form').submit();">
                          <label class="form-check-label" for="light-check">Light</label>
                      </div>
                      <div class="form-check form-radio-success mt-1">
                          <input type="radio" id="dark-check" name="left_sidebar_theme" class="form-check-input"
                              value="dark" onclick="event.preventDefault(); this.closest('form').submit();">
                          <label class="form-check-label" for="dark-check">Dark</label>
                      </div>
                  </div>
              </form>

              <form method="POST" action="{{ route('settings.update', Auth::id()) }}">
                @csrf
                @method('PUT')
                <div class="mt-3">
                    <div class="form-check form-radio-success mt-3">
                        <input type="radio" id="fixed-check" name="left_sidebar_compact" class="form-check-input"
                            value="fixed" onclick="event.preventDefault(); this.closest('form').submit();">
                        <label class="form-check-label" for="fixed-check">Fixed</label>
                    </div>

                    <div class="form-check form-radio-success mt-1">
                        <input type="radio" id="condensed-check" name="left_sidebar_compact"
                            class="form-check-input" value="condensed"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        <label class="form-check-label" for="condensed-check">Condensed</label>
                    </div>
                    <div class="form-check form-radio-success mt-1">
                        <input type="radio" id="scrollable-check" name="left_sidebar_compact"
                            class="form-check-input" value="scrollable"
                            onclick="event.preventDefault(); this.closest('form').submit();">
                        <label class="form-check-label" for="scrollable-check">Scrollable</label>
                    </div>
                </div>
            </form>

              <div class="d-grid mt-4">
                  <a href="#" class="btn btn-outline-danger mt-3" target="_blank"><i class="uil-comment-question me-1"></i>
                      Report issue to admin</a>
              </div>
          </div> <!-- end padding-->

      </div>
  </div>

  <div class="rightbar-overlay"></div>
  <!-- /End-bar -->
