<?php // THEMORA SHOP — Signup Page ?>
<div style="min-height:80vh;display:flex;align-items:center;justify-content:center;padding:3rem 1.25rem;position:relative">
  <div style="position:absolute;inset:0;background:radial-gradient(ellipse 60% 50% at 50% 0%,rgba(99,102,241,.12),transparent 70%);pointer-events:none"></div>
  <div style="max-width:480px;width:100%;z-index:1">
    <div style="text-align:center;margin-bottom:2rem">
      <a href="<?= url('/') ?>" class="site-logo" style="font-size:1.5rem;display:inline-block;margin-bottom:1.5rem"><i class="bi bi-lightning-charge-fill"></i> <?= e(setting('site_name')) ?></a>
      <h1 style="font-size:1.75rem;margin-bottom:.5rem">Create your account</h1>
      <p style="color:var(--text-muted);font-size:.9rem">Join thousands of creators. It's free!</p>
    </div>
    <div class="card">
      <div class="card-body" style="padding:2rem">
        <?php if ($error): ?><div class="alert alert-error"><i class="bi bi-exclamation-circle-fill"></i> <?= $error ?></div><?php endif; ?>
        <form method="POST" action="<?= url('signup') ?>">
          <?= csrf_field() ?>
          <?php $ref = $_GET['ref'] ?? ''; if ($ref): ?><input type="hidden" name="ref" value="<?= e($ref) ?>"><?php endif; ?>
          <div class="form-group">
            <label class="form-label" for="name">Full Name</label>
            <input type="text" name="name" id="name" class="form-control" placeholder="John Smith" value="<?= old('name') ?>" required autocomplete="name">
          </div>
          <div class="form-group">
            <label class="form-label" for="email">Email Address</label>
            <input type="email" name="email" id="email" class="form-control" placeholder="you@example.com" value="<?= e(old('email')) ?>" required autocomplete="email">
          </div>
          <div class="form-group">
            <label class="form-label" for="password">Password</label>
            <div style="position:relative">
              <input type="password" name="password" id="password" class="form-control" placeholder="Min 8 characters" required autocomplete="new-password" oninput="checkStrength(this.value)">
              <button type="button" onclick="togglePwd('password','eye1')" style="position:absolute;right:.875rem;top:50%;transform:translateY(-50%);background:none;border:none;color:var(--text-dim);cursor:pointer"><i class="bi bi-eye" id="eye1"></i></button>
            </div>
            <div id="strength-bar" style="height:3px;border-radius:99px;margin-top:.5rem;background:var(--bg-3);overflow:hidden"><div id="strength-fill" style="height:100%;width:0%;transition:width .3s,background .3s"></div></div>
            <div id="strength-label" style="font-size:.72rem;color:var(--text-dim);margin-top:.25rem"></div>
          </div>
          <div class="form-group">
            <label class="form-label" for="confirm">Confirm Password</label>
            <input type="password" name="password_confirm" id="confirm" class="form-control" placeholder="Repeat password" required>
          </div>
          <?php if ($ref): ?>
          <div class="alert alert-success" style="margin-bottom:1rem"><i class="bi bi-gift-fill"></i> You were referred by a friend! You'll both get rewarded.</div>
          <?php endif; ?>
          <div class="form-check" style="margin-bottom:1.25rem">
            <input type="checkbox" id="terms" required>
            <label for="terms" style="font-size:.85rem;color:var(--text-muted)">I agree to the <a href="#" style="color:var(--accent-light)">Terms of Service</a> and <a href="#" style="color:var(--accent-light)">Privacy Policy</a></label>
          </div>
          <button type="submit" class="btn btn-primary btn-full btn-lg"><i class="bi bi-person-plus"></i> Create Account</button>
        </form>
        <div style="display:flex;align-items:center;gap:1rem;margin:1.5rem 0">
          <div style="flex:1;height:1px;background:var(--border)"></div>
          <span style="font-size:.8rem;color:var(--text-dim)">or</span>
          <div style="flex:1;height:1px;background:var(--border)"></div>
        </div>
        <div style="display:grid;grid-template-columns:1fr 1fr;gap:.75rem">
          <a href="<?= url('auth/google') ?>" class="btn btn-secondary" style="justify-content:center"><img src="https://www.svgrepo.com/show/475656/google-color.svg" alt="" style="width:18px"> Google</a>
          <a href="<?= url('auth/github') ?>" class="btn btn-secondary" style="justify-content:center"><i class="bi bi-github"></i> GitHub</a>
        </div>
      </div>
      <div class="card-footer" style="text-align:center;font-size:.875rem;color:var(--text-muted)">Already have an account? <a href="<?= url('login') ?>" style="color:var(--accent-light);font-weight:600">Sign in</a></div>
    </div>
  </div>
</div>
<script>
function togglePwd(i,e){const x=document.getElementById(i),ic=document.getElementById(e);x.type=x.type==='password'?'text':'password';ic.className='bi bi-eye'+(x.type==='text'?'-slash':'');}
function checkStrength(v){const f=document.getElementById('strength-fill'),l=document.getElementById('strength-label');let s=0;if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^A-Za-z0-9]/.test(v))s++;const w=['0%','25%','50%','75%','100%'][s],c=['#ef4444','#f59e0b','#f59e0b','#22c55e','#6366f1'][s],t=['','Weak','Fair','Good','Strong'][s];f.style.width=w;f.style.background=c;l.textContent=t;l.style.color=c;}
</script>
