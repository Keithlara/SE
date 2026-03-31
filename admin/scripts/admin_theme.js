(function () {
  const MODE_KEY = 'adminThemeMode';
  const LEGACY_MODE_KEY = 'adminTheme';
  const PRESET_KEY = 'adminThemePreset';
  const ALLOWED_MODES = ['light', 'dark'];
  const ALLOWED_PRESETS = ['ocean', 'emerald', 'sunset', 'plum'];
  const PRESET_LABELS = {
    ocean: 'Ocean Blue',
    emerald: 'Emerald',
    sunset: 'Sunset',
    plum: 'Plum'
  };

  function safeStorageGet(key) {
    try {
      return window.localStorage.getItem(key);
    } catch (error) {
      return null;
    }
  }

  function safeStorageSet(key, value) {
    try {
      window.localStorage.setItem(key, value);
    } catch (error) {
      return false;
    }

    return true;
  }

  function sanitizeMode(mode) {
    return ALLOWED_MODES.includes(mode) ? mode : 'light';
  }

  function sanitizePreset(preset) {
    return ALLOWED_PRESETS.includes(preset) ? preset : 'ocean';
  }

  function readAdminThemePreferences() {
    let mode = sanitizeMode(safeStorageGet(MODE_KEY));
    const legacyMode = safeStorageGet(LEGACY_MODE_KEY);
    let preset = sanitizePreset(safeStorageGet(PRESET_KEY));

    if (!ALLOWED_MODES.includes(safeStorageGet(MODE_KEY)) && (legacyMode === 'dark' || legacyMode === 'light')) {
      mode = legacyMode;
    }

    return { mode, preset };
  }

  function applyAdminThemePreferences(preferences) {
    const prefs = {
      mode: sanitizeMode(preferences && preferences.mode),
      preset: sanitizePreset(preferences && preferences.preset)
    };

    const html = document.documentElement;
    const body = document.body;

    html.setAttribute('data-admin-mode', prefs.mode);
    html.setAttribute('data-admin-theme', prefs.preset);
    html.classList.toggle('dark-mode', prefs.mode === 'dark');

    if (body) {
      body.classList.toggle('dark-mode', prefs.mode === 'dark');
      body.setAttribute('data-admin-mode', prefs.mode);
      body.setAttribute('data-admin-theme', prefs.preset);
    }

    safeStorageSet(MODE_KEY, prefs.mode);
    safeStorageSet(PRESET_KEY, prefs.preset);
    safeStorageSet(LEGACY_MODE_KEY, prefs.mode);

    syncAdminThemeControls();
    return prefs;
  }

  function setAdminThemeMode(mode) {
    const prefs = readAdminThemePreferences();
    prefs.mode = sanitizeMode(mode);
    return applyAdminThemePreferences(prefs);
  }

  function setAdminThemePreset(preset) {
    const prefs = readAdminThemePreferences();
    prefs.preset = sanitizePreset(preset);
    return applyAdminThemePreferences(prefs);
  }

  function toggleAdminThemeMode() {
    const prefs = readAdminThemePreferences();
    prefs.mode = prefs.mode === 'dark' ? 'light' : 'dark';
    applyAdminThemePreferences(prefs);
    return false;
  }

  function syncAdminThemeControls() {
    const prefs = readAdminThemePreferences();

    document.querySelectorAll('[data-admin-mode-choice]').forEach((button) => {
      const isActive = button.getAttribute('data-admin-mode-choice') === prefs.mode;
      button.classList.toggle('is-active', isActive);
      button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });

    document.querySelectorAll('[data-admin-theme-choice]').forEach((button) => {
      const isActive = button.getAttribute('data-admin-theme-choice') === prefs.preset;
      button.classList.toggle('is-active', isActive);
      button.setAttribute('aria-pressed', isActive ? 'true' : 'false');
    });

    const modeToggle = document.getElementById('modeToggle');
    const modeIcon = document.getElementById('modeIcon');
    if (modeIcon) {
      modeIcon.className = prefs.mode === 'dark' ? 'bi bi-sun-fill' : 'bi bi-moon-stars-fill';
    }

    if (modeToggle) {
      modeToggle.setAttribute('aria-pressed', prefs.mode === 'dark' ? 'true' : 'false');
      modeToggle.setAttribute('title', prefs.mode === 'dark' ? 'Switch to light mode' : 'Switch to dark mode');
    }

    const currentLabel = document.getElementById('currentThemeLabel');
    if (currentLabel) {
      currentLabel.textContent = PRESET_LABELS[prefs.preset] || 'Ocean Blue';
    }

    const profileSummary = document.getElementById('profileThemeSummary');
    if (profileSummary) {
      const modeLabel = prefs.mode === 'dark' ? 'Dark' : 'Light';
      profileSummary.textContent = `${modeLabel} · ${PRESET_LABELS[prefs.preset] || 'Ocean Blue'}`;
    }
  }

  window.readAdminThemePreferences = readAdminThemePreferences;
  window.applyAdminThemePreferences = applyAdminThemePreferences;
  window.setAdminThemeMode = setAdminThemeMode;
  window.setAdminThemePreset = setAdminThemePreset;
  window.toggleAdminThemeMode = toggleAdminThemeMode;
  window.toggleAdminTheme = toggleAdminThemeMode;
  window.syncAdminThemeControls = syncAdminThemeControls;

  document.addEventListener('DOMContentLoaded', () => {
    applyAdminThemePreferences(readAdminThemePreferences());

    document.querySelectorAll('[data-admin-mode-choice]').forEach((button) => {
      button.addEventListener('click', () => {
        setAdminThemeMode(button.getAttribute('data-admin-mode-choice'));
      });
    });

    document.querySelectorAll('[data-admin-theme-choice]').forEach((button) => {
      button.addEventListener('click', () => {
        setAdminThemePreset(button.getAttribute('data-admin-theme-choice'));
      });
    });
  });
})();
