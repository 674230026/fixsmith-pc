import { useEffect, useMemo, useRef, useState } from "react";

type Role = "customer" | "admin" | "technician";
type CustomerPage = "overview" | "new-repair" | "repairs" | "profile" | "complaint";
type AdminPage = "dashboard" | "requests" | "accounts" | "team" | "complaints" | "profile";
type TechnicianPage = "workspace" | "assigned" | "complaints" | "profile";
type IconName =
  | "activity"
  | "arrow"
  | "bell"
  | "box"
  | "calendar"
  | "check"
  | "chevron"
  | "clock"
  | "computer"
  | "dashboard"
  | "edit"
  | "file"
  | "inbox"
  | "info"
  | "logout"
  | "mail"
  | "map"
  | "menu"
  | "phone"
  | "pause"
  | "play"
  | "plus"
  | "profile"
  | "search"
  | "settings"
  | "star"
  | "tool"
  | "upload"
  | "users";

const iconPaths: Record<IconName, React.ReactNode> = {
  activity: <><path d="M4 12h3l2-6 4 12 2-6h5" /></>,
  arrow: <><path d="m9 18 6-6-6-6" /></>,
  bell: <><path d="M18 8a6 6 0 0 0-12 0c0 7-3 7-3 9h18c0-2-3-2-3-9" /><path d="M13.7 21a2 2 0 0 1-3.4 0" /></>,
  box: <><path d="m21 8-9 5-9-5" /><path d="m3 8 9-5 9 5v8l-9 5-9-5Z" /><path d="M12 13v8" /></>,
  calendar: <><rect x="3" y="5" width="18" height="16" rx="2" /><path d="M16 3v4M8 3v4M3 10h18" /></>,
  check: <path d="m5 12 4 4L19 6" />,
  chevron: <path d="m9 18 6-6-6-6" />,
  clock: <><circle cx="12" cy="12" r="9" /><path d="M12 7v5l3 2" /></>,
  computer: <><rect x="3" y="4" width="18" height="13" rx="2" /><path d="M8 21h8M12 17v4" /></>,
  dashboard: <><rect x="3" y="3" width="7" height="7" rx="1" /><rect x="14" y="3" width="7" height="7" rx="1" /><rect x="3" y="14" width="7" height="7" rx="1" /><rect x="14" y="14" width="7" height="7" rx="1" /></>,
  edit: <><path d="M12 20h9" /><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L8 18l-4 1 1-4Z" /></>,
  file: <><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8Z" /><path d="M14 2v6h6M8 13h8M8 17h6" /></>,
  inbox: <><path d="M4 4h16v16H4Z" /><path d="M4 14h5l2 3h2l2-3h5" /></>,
  info: <><circle cx="12" cy="12" r="9" /><path d="M12 11v5M12 8h.01" /></>,
  logout: <><path d="M10 17l5-5-5-5M15 12H3" /><path d="M15 4h4a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2h-4" /></>,
  mail: <><rect x="3" y="5" width="18" height="14" rx="2" /><path d="m3 7 9 6 9-6" /></>,
  map: <><path d="m3 6 6-3 6 3 6-3v15l-6 3-6-3-6 3Z" /><path d="M9 3v15M15 6v15" /></>,
  menu: <><path d="M4 7h16M4 12h16M4 17h16" /></>,
  phone: <path d="M22 16.9v3a2 2 0 0 1-2.2 2 19.8 19.8 0 0 1-8.6-3.1 19.4 19.4 0 0 1-6-6A19.8 19.8 0 0 1 2.1 4.2 2 2 0 0 1 4.1 2h3a2 2 0 0 1 2 1.7c.1 1 .4 2 .7 2.8a2 2 0 0 1-.4 2.1L8.1 9.9a16 16 0 0 0 6 6l1.3-1.3a2 2 0 0 1 2.1-.4 13 13 0 0 0 2.8.7 2 2 0 0 1 1.7 2Z" />,
  pause: <><path d="M9 5v14M15 5v14" /></>,
  play: <path d="m8 5 11 7-11 7Z" />,
  plus: <path d="M12 5v14M5 12h14" />,
  profile: <><circle cx="12" cy="8" r="4" /><path d="M4 21a8 8 0 0 1 16 0" /></>,
  search: <><circle cx="11" cy="11" r="7" /><path d="m20 20-4-4" /></>,
  settings: <><circle cx="12" cy="12" r="3" /><path d="M19.4 15a1.7 1.7 0 0 0 .3 1.9l.1.1-2.8 2.8-.1-.1a1.7 1.7 0 0 0-1.9-.3 1.7 1.7 0 0 0-1 1.6v.2h-4V21a1.7 1.7 0 0 0-1-1.6 1.7 1.7 0 0 0-1.9.3l-.1.1L4.2 17l.1-.1a1.7 1.7 0 0 0 .3-1.9A1.7 1.7 0 0 0 3 14H2.8v-4H3a1.7 1.7 0 0 0 1.6-1 1.7 1.7 0 0 0-.3-1.9L4.2 7 7 4.2l.1.1a1.7 1.7 0 0 0 1.9.3A1.7 1.7 0 0 0 10 3V2.8h4V3a1.7 1.7 0 0 0 1 1.6 1.7 1.7 0 0 0 1.9-.3l.1-.1L19.8 7l-.1.1a1.7 1.7 0 0 0-.3 1.9 1.7 1.7 0 0 0 1.6 1h.2v4H21a1.7 1.7 0 0 0-1.6 1Z" /></>,
  star: <path d="m12 3 2.8 5.7 6.2.9-4.5 4.4 1.1 6.2-5.6-2.9-5.6 2.9 1.1-6.2L3 9.6l6.2-.9Z" />,
  tool: <path d="M14.7 6.3a4 4 0 0 0-5-5L12 3.6 8.6 7 6.3 4.7a4 4 0 0 0 5 5L4 17l3 3 7.3-7.3a4 4 0 0 0 5-5L17 10l-3.4-3.4Z" />,
  upload: <><path d="M12 16V4M7 9l5-5 5 5" /><path d="M5 14v5h14v-5" /></>,
  users: <><path d="M16 21v-2a4 4 0 0 0-4-4H6a4 4 0 0 0-4 4v2" /><circle cx="9" cy="7" r="4" /><path d="M22 21v-2a4 4 0 0 0-3-3.9M16 3.1a4 4 0 0 1 0 7.8" /></>,
};

function Icon({ name, size = 18 }: { name: IconName; size?: number }) {
  return <svg aria-hidden="true" className="icon" width={size} height={size} viewBox="0 0 24 24" fill="none" stroke="currentColor" strokeWidth="1.8" strokeLinecap="round" strokeLinejoin="round">{iconPaths[name]}</svg>;
}

const repairs = [
  { id: "FX-1048", customer: "Maya Chen", device: "ASUS ROG Zephyrus G14", serial: "M7NRKD029184", issue: "Overheating and sudden shutdowns", method: "Mail-in", status: "Diagnosing", tech: "Alex Kim", created: "Jun 12" },
  { id: "FX-1047", customer: "Jordan Blake", device: "Custom Desktop PC", serial: "CST-445-9021", issue: "No display after BIOS update", method: "Drop-off", status: "Waiting for part", tech: "Sam Rivera", created: "Jun 11" },
  { id: "FX-1046", customer: "Noah Wilson", device: "Dell XPS 15 9520", serial: "DX9520-77182", issue: "Battery not charging", method: "Mail-in", status: "Fixing", tech: "Alex Kim", created: "Jun 10" },
  { id: "FX-1045", customer: "Sofia Martinez", device: "Lenovo ThinkPad X1", serial: "PF4K9M21", issue: "Keyboard keys unresponsive", method: "Drop-off", status: "Pending", tech: "Unassigned", created: "Jun 10" },
];

const allStatuses = ["Package received", "Diagnosing", "Fixing", "Waiting for part", "Pending", "Finished"];
const customerRepairs = [
  { id: "FX-1048", device: "ASUS ROG Zephyrus G14", serial: "M7NRKD029184", method: "Mail-in", status: "Diagnosing", date: "Jun 12, 2025", update: "Alex is running a full hardware diagnostic and thermal test.", activeStep: 1 },
  { id: "FX-0982", device: "MacBook Pro 14-inch", serial: "C02XH04AJG5H", method: "Drop-off", status: "Finished", date: "Mar 04, 2025", update: "Battery replacement is complete and the device passed final testing.", activeStep: 3 },
  { id: "FX-0871", device: "Custom Desktop PC", serial: "CST-445-8110", method: "Mail-in", status: "Finished", date: "Nov 18, 2024", update: "Graphics card and power supply were replaced. Repair completed.", activeStep: 4 },
];
const feedbackRecords = [
  { id: "FDB-205", type: "Rating", repair: "FX-1048", customer: "Maya Chen", subject: "Excellent diagnostic service", message: "Alex explained the thermal issue clearly and kept me informed.", age: "8 min ago", status: "New", rating: 5 },
  { id: "CMP-204", type: "Complaint", repair: "FX-1048", customer: "Maya Chen", subject: "No update after diagnostic", message: "I was expecting an update yesterday and have not heard back yet.", age: "18 min ago", status: "New", rating: 0 },
  { id: "FDB-203", type: "Rating", repair: "FX-1046", customer: "Noah Wilson", subject: "Helpful repair experience", message: "The repair was handled professionally and the update notes were useful.", age: "Yesterday", status: "Reviewed", rating: 4 },
  { id: "CMP-198", type: "Complaint", repair: "FX-1046", customer: "Noah Wilson", subject: "Charging issue returned", message: "The laptop stopped charging again after the repair. Please advise on the next step.", age: "2 days ago", status: "In review", rating: 0 },
];

function StatusPill({ status }: { status: string }) {
  const key = status.toLowerCase().replaceAll(" ", "-");
  return <span className={`status-pill status-${key}`}><span className="status-dot" />{status}</span>;
}

function Button({ children, variant = "primary", icon, onClick, type = "button", disabled = false }: { children: React.ReactNode; variant?: "primary" | "secondary" | "ghost"; icon?: IconName; onClick?: () => void; type?: "button" | "submit"; disabled?: boolean }) {
  return <button className={`button button-${variant}`} onClick={onClick} type={type} disabled={disabled}>{icon && <Icon name={icon} />}{children}</button>;
}

function Field({ label, value, onChange, placeholder, type = "text" }: { label: string; value: string; onChange: (value: string) => void; placeholder?: string; type?: string }) {
  return <label className="field"><span>{label}</span><input value={value} onChange={(event) => onChange(event.target.value)} placeholder={placeholder} type={type} /></label>;
}

function Header({ role, setRole, onMenu, onProfile, onLogout, onNotifications }: { role: Role; setRole: (role: Role) => void; onMenu: () => void; onProfile: () => void; onLogout: () => void; onNotifications: () => void }) {
  const [profileMenuOpen, setProfileMenuOpen] = useState(false);
  return (
    <header className="topbar">
      <button className="mobile-menu" onClick={onMenu} aria-label="Open navigation"><Icon name="menu" /></button>
      <div className="mobile-brand"><LogoMark /><strong>Fixsmith</strong></div>
      <div className="role-switch" aria-label="Preview role">
        {(["customer", "admin", "technician"] as Role[]).map((item) => (
          <button key={item} className={role === item ? "active" : ""} onClick={() => setRole(item)}>{item === "technician" ? "Technician" : item[0].toUpperCase() + item.slice(1)}</button>
        ))}
      </div>
      <div className="header-actions">
        <button className="icon-button" aria-label="Open notifications" onClick={onNotifications}><Icon name="bell" /><span className="notification-dot" /></button>
        <button className="profile-trigger" onClick={() => setProfileMenuOpen(!profileMenuOpen)} aria-expanded={profileMenuOpen}>
          <span className="avatar">{role === "admin" ? "EW" : role === "technician" ? "AK" : "MC"}</span>
          <span className="user-meta"><strong>{role === "admin" ? "Emma Walsh" : role === "technician" ? "Alex Kim" : "Maya Chen"}</strong><span>{role === "admin" ? "Administrator" : role === "technician" ? "Senior technician" : "Customer"}</span></span>
          <Icon name="chevron" size={15} />
        </button>
        {profileMenuOpen && <div className="profile-menu">
          <button onClick={() => { onProfile(); setProfileMenuOpen(false); }}><Icon name="profile" /><span><strong>Profile</strong><small>Edit your personal details</small></span></button>
          <button className="menu-logout" onClick={() => { onLogout(); setProfileMenuOpen(false); }}><Icon name="logout" /><span><strong>Log out</strong><small>End your current session</small></span></button>
        </div>}
      </div>
    </header>
  );
}

function NotificationDrawer({ role, open, onClose, onNavigate }: { role: Role; open: boolean; onClose: () => void; onNavigate: (target: string, entityId?: string) => void }) {
  const roleNotifications = {
    customer: [
      { id: 1, icon: "tool" as IconName, title: "Diagnostic update ready", message: "Alex added a new note to repair FX-1048.", time: "12 minutes ago", upcoming: false, target: "repairs", entityId: "FX-1048" },
      { id: 2, icon: "calendar" as IconName, title: "Expected workshop update", message: "Your next repair update is expected tomorrow by 5:00 PM.", time: "Tomorrow, 5:00 PM", upcoming: true, target: "repairs", entityId: "FX-1048" },
      { id: 3, icon: "box" as IconName, title: "Package delivered", message: "Your device was delivered safely to the Fixsmith workshop.", time: "Yesterday", upcoming: false, target: "repairs", entityId: "FX-1048" },
    ],
    admin: [
      { id: 1, icon: "inbox" as IconName, title: "Four requests need assignment", message: "New repair requests are waiting in the dispatch queue.", time: "8 minutes ago", upcoming: false, target: "team" },
      { id: 2, icon: "calendar" as IconName, title: "Weekly operations summary", message: "The workshop performance report will be ready for review.", time: "Friday, 4:00 PM", upcoming: true, target: "dashboard" },
      { id: 3, icon: "star" as IconName, title: "New customer feedback", message: "Maya Chen submitted a five-star rating for FX-1048.", time: "32 minutes ago", upcoming: false, target: "complaints", entityId: "FDB-205" },
    ],
    technician: [
      { id: 1, icon: "tool" as IconName, title: "New repair assigned", message: "Repair FX-1046 was added to your active queue.", time: "5 minutes ago", upcoming: false, target: "assigned", entityId: "FX-1046" },
      { id: 2, icon: "clock" as IconName, title: "Customer update due", message: "FX-1048 needs a customer-facing progress note.", time: "Today, 3:30 PM", upcoming: true, target: "assigned", entityId: "FX-1048" },
      { id: 3, icon: "star" as IconName, title: "Customer left feedback", message: "You received a five-star rating from Maya Chen.", time: "1 hour ago", upcoming: false, target: "complaints", entityId: "FDB-205" },
    ],
  };
  const [readIds, setReadIds] = useState<number[]>([]);
  useEffect(() => setReadIds([]), [role]);
  const notifications = roleNotifications[role];
  const unreadCount = notifications.filter((item) => !readIds.includes(item.id)).length;
  const openNotification = (item: typeof notifications[number]) => {
    setReadIds([...new Set([...readIds, item.id])]);
    onNavigate(item.target, item.entityId);
    onClose();
  };
  return (
    <>
      {open && <button className="notification-scrim" aria-label="Close notifications" onClick={onClose} />}
      <aside className={`notification-drawer ${open ? "open" : ""}`} aria-hidden={!open}>
        <div className="notification-head"><div><span className="kicker">Activity center</span><h2>Notifications</h2><p>{unreadCount > 0 ? `${unreadCount} unread updates` : "You’re all caught up"}</p></div><button className="modal-close" onClick={onClose} aria-label="Close notifications">×</button></div>
        <div className="notification-tools"><button onClick={() => setReadIds(notifications.map((item) => item.id))}><Icon name="check" />Mark all as read</button><button><Icon name="settings" />Preferences</button></div>
        <div className="notification-section">
          <div className="notification-section-title"><span>Upcoming</span><small>Scheduled reminders</small></div>
          {notifications.filter((item) => item.upcoming).map((item) => <button className="notification-item upcoming" key={item.id} onClick={() => openNotification(item)}><span className="notification-icon"><Icon name={item.icon} /></span><span><strong>{item.title}</strong><p>{item.message}</p><small><Icon name="calendar" size={12} />{item.time}</small></span>{!readIds.includes(item.id) && <i />}</button>)}
        </div>
        <div className="notification-section">
          <div className="notification-section-title"><span>Recent</span><small>Latest account activity</small></div>
          {notifications.filter((item) => !item.upcoming).map((item) => <button className={`notification-item ${readIds.includes(item.id) ? "read" : ""}`} key={item.id} onClick={() => openNotification(item)}><span className="notification-icon"><Icon name={item.icon} /></span><span><strong>{item.title}</strong><p>{item.message}</p><small><Icon name="clock" size={12} />{item.time}</small></span>{!readIds.includes(item.id) && <i />}</button>)}
        </div>
        <div className="notification-footer"><Icon name="bell" /><p>Future repair updates and reminders will appear here automatically.</p></div>
      </aside>
    </>
  );
}

function LogoMark() {
  return <span className="logo-mark"><Icon name="tool" size={20} /></span>;
}

function Sidebar({ role, open, close, customerPage, onCustomerNavigate, adminPage, onAdminNavigate, technicianPage, onTechnicianNavigate, onLogout }: { role: Role; open: boolean; close: () => void; customerPage: CustomerPage; onCustomerNavigate: (page: CustomerPage) => void; adminPage: AdminPage; onAdminNavigate: (page: AdminPage) => void; technicianPage: TechnicianPage; onTechnicianNavigate: (page: TechnicianPage) => void; onLogout: () => void }) {
  const items = role === "customer"
    ? [["dashboard", "Overview", "overview"], ["plus", "New repair", "new-repair"], ["clock", "My repairs", "repairs"], ["star", "Rate & complain", "complaint"], ["profile", "Profile", "profile"]]
    : role === "admin"
      ? [["dashboard", "Dashboard", "dashboard"], ["inbox", "Repair requests", "requests"], ["users", "Accounts", "accounts"], ["tool", "Manage team", "team"], ["star", "Customer feedback", "complaints"], ["profile", "Profile", "profile"]]
      : [["dashboard", "My workspace", "workspace"], ["tool", "Assigned repairs", "assigned"], ["star", "Customer feedback", "complaints"], ["profile", "Profile", "profile"]];
  return (
    <>
      {open && <button className="sidebar-scrim" aria-label="Close navigation" onClick={close} />}
      <aside className={`sidebar ${open ? "open" : ""}`}>
        <div className="brand"><LogoMark /><span><strong>Fixsmith</strong><small>PC repair studio</small></span></div>
        <nav>
          <p className="nav-label">Workspace</p>
          {items.map(([icon, label, page]) => <button key={label} className={role === "customer" ? `nav-item ${customerPage === page ? "active" : ""}` : role === "admin" ? `nav-item ${adminPage === page ? "active" : ""}` : `nav-item ${technicianPage === page ? "active" : ""}`} onClick={() => { if (role === "customer") onCustomerNavigate(page as CustomerPage); if (role === "admin") onAdminNavigate(page as AdminPage); if (role === "technician") onTechnicianNavigate(page as TechnicianPage); close(); }}><Icon name={icon as IconName} />{label}{label === "Repair requests" && <span className="nav-count">{repairs.length}</span>}{label === "Assigned repairs" && <span className="nav-count">2</span>}</button>)}
        </nav>
        <div className="sidebar-bottom">
          <div className="support-card"><span className="support-icon"><Icon name="phone" /></span><strong>Need help?</strong><p>Call our workshop</p><a href="tel:+12025550148">+1 (202) 555-0148</a></div>
          <button className="nav-item logout" onClick={onLogout}><Icon name="logout" />Log out</button>
        </div>
      </aside>
    </>
  );
}

function PageHeading({ eyebrow, title, description, action }: { eyebrow?: string; title: string; description: string; action?: React.ReactNode }) {
  return <div className="page-heading"><div>{eyebrow && <p className="eyebrow">{eyebrow}</p>}<h1>{title}</h1><p>{description}</p></div>{action}</div>;
}

function NewRepairPage({ onDone }: { onDone: () => void }) {
  const [delivery, setDelivery] = useState<"mail" | "dropoff">("mail");
  const [serial, setSerial] = useState("");
  const [symptom, setSymptom] = useState("");
  const [deviceFile, setDeviceFile] = useState("");
  const [slipFile, setSlipFile] = useState("");
  const [submitted, setSubmitted] = useState(false);
  const imageRef = useRef<HTMLInputElement>(null);
  const slipRef = useRef<HTMLInputElement>(null);

  const submitRepair = (event: React.FormEvent) => {
    event.preventDefault();
    setSubmitted(true);
  };

  return (
    <div className="content">
      <PageHeading eyebrow="New repair" title="Tell us about your PC" description="Share a few details and we’ll take it from here." />
      <form className="panel form-panel standalone-card" onSubmit={submitRepair}>
        <div className="panel-head"><div><span className="kicker">Repair request</span><h2>Device details</h2><p>Give our technicians enough detail to prepare for your repair.</p></div><span className="step-badge">01</span></div>
        <Field label="PC serial number" value={serial} onChange={setSerial} placeholder="e.g. M7NRKD029184" />
        <label className="field"><span>What’s going wrong?</span><textarea value={symptom} onChange={(event) => setSymptom(event.target.value)} placeholder="Describe the symptoms, when they started, and anything you’ve already tried." /></label>
        <div className="upload-zone" onClick={() => imageRef.current?.click()} role="button" tabIndex={0}>
          <input ref={imageRef} type="file" accept="image/*" onChange={(event) => setDeviceFile(event.target.files?.[0]?.name ?? "")} />
          <span><Icon name={deviceFile ? "check" : "upload"} /></span>
          <div><strong>{deviceFile || "Add photos of the issue"}</strong><p>{deviceFile ? "Image ready to upload" : "PNG or JPG, up to 10 MB"}</p></div>
        </div>
        <fieldset className="delivery-fieldset">
          <legend>How will your device reach us?</legend>
          <label className={delivery === "mail" ? "delivery-option selected" : "delivery-option"}><input type="radio" name="delivery" checked={delivery === "mail"} onChange={() => setDelivery("mail")} /><span className="radio-mark" /><Icon name="mail" /><div><strong>Mail to workshop</strong><p>Ship it to us using a tracked service</p></div></label>
          <label className={delivery === "dropoff" ? "delivery-option selected" : "delivery-option"}><input type="radio" name="delivery" checked={delivery === "dropoff"} onChange={() => setDelivery("dropoff")} /><span className="radio-mark" /><Icon name="map" /><div><strong>Bring it yourself</strong><p>Drop off at our downtown workshop</p></div></label>
        </fieldset>
        {delivery === "mail" && <div className="slip-upload"><div><Icon name="file" /><span><strong>Shipping slip</strong><small>Upload your courier receipt or tracking slip.</small></span></div><input ref={slipRef} type="file" accept="image/*,.pdf" onChange={(event) => setSlipFile(event.target.files?.[0]?.name ?? "")} /><Button variant="secondary" icon={slipFile ? "check" : "upload"} onClick={() => slipRef.current?.click()}>{slipFile || "Upload slip"}</Button></div>}
        <Button type="submit" icon="arrow" disabled={!serial || !symptom}>{submitted ? "Request received" : "Submit repair request"}</Button>
        {submitted && <div className="submission-confirmation"><span><Icon name="check" /></span><div><strong>Your repair request is ready</strong><p>We’ll email your next steps shortly.</p></div><Button variant="ghost" onClick={onDone}>View my repairs</Button></div>}
      </form>
    </div>
  );
}

function ProfilePage() {
  const [editing, setEditing] = useState(false);
  const [name, setName] = useState("Maya Chen");
  const [phone, setPhone] = useState("+1 (202) 555-0182");
  return (
    <div className="content">
      <PageHeading eyebrow="Your account" title="Profile & contact details" description="Keep your information current so the workshop can reach you." />
      <section className="panel profile-panel standalone-card">
        <div className="panel-head"><div><span className="kicker">Personal information</span><h2>Your profile</h2></div><button className="text-button" onClick={() => setEditing(!editing)}><Icon name={editing ? "check" : "edit"} />{editing ? "Save changes" : "Edit profile"}</button></div>
        <div className="profile-identity"><div className="large-avatar">MC</div><div><strong>{name}</strong><p>Customer since Aug 2023</p></div></div>
        <div className="profile-fields">
          {editing ? <><Field label="Full name" value={name} onChange={setName} /><Field label="Telephone number" value={phone} onChange={setPhone} type="tel" /></> : <>
            <div><span>Full name</span><strong>{name}</strong></div>
            <div><span>Telephone number</span><strong>{phone}</strong></div>
            <div><span>Email address</span><strong>maya.chen@example.com</strong></div>
          </>}
        </div>
        <div className="workshop-card"><Icon name="map" /><div><strong>Fixsmith Workshop</strong><p>84 Mercer Street, Brooklyn, NY<br />Mon–Sat, 9:00 AM–6:00 PM</p></div></div>
      </section>
    </div>
  );
}

function StaffProfilePage({ role }: { role: "admin" | "technician" }) {
  const isAdmin = role === "admin";
  const [editing, setEditing] = useState(false);
  const [name, setName] = useState(isAdmin ? "Emma Walsh" : "Alex Kim");
  const [phone, setPhone] = useState(isAdmin ? "+1 (202) 555-0114" : "+1 (202) 555-0166");
  const [email, setEmail] = useState(isAdmin ? "emma.walsh@fixsmith.com" : "alex.kim@fixsmith.com");
  const initials = name.split(" ").map((word) => word[0]).join("").slice(0, 2).toUpperCase();
  return (
    <div className="content">
      <PageHeading eyebrow={`${isAdmin ? "Administrator" : "Technician"} account`} title="Profile & contact details" description="Keep your workshop identity and contact information up to date." />
      <section className="panel profile-panel standalone-card">
        <div className="panel-head"><div><span className="kicker">Personal information</span><h2>Your profile</h2></div><button className="text-button" onClick={() => setEditing(!editing)}><Icon name={editing ? "check" : "edit"} />{editing ? "Save changes" : "Edit profile"}</button></div>
        <div className="profile-identity"><div className="large-avatar">{initials}</div><div><strong>{name}</strong><p>{isAdmin ? "Workshop administrator" : "Senior technician"} · Active account</p></div></div>
        <div className="profile-fields">
          {editing ? <><Field label="Full name" value={name} onChange={setName} /><Field label="Email address" value={email} onChange={setEmail} type="email" /><Field label="Telephone number" value={phone} onChange={setPhone} type="tel" /></> : <>
            <div><span>Full name</span><strong>{name}</strong></div>
            <div><span>Email address</span><strong>{email}</strong></div>
            <div><span>Telephone number</span><strong>{phone}</strong></div>
            <div><span>Role</span><strong>{isAdmin ? "Administrator" : "Senior technician"}</strong></div>
          </>}
        </div>
        <div className="workshop-card"><Icon name={isAdmin ? "settings" : "tool"} /><div><strong>{isAdmin ? "Administrative access" : "Technician access"}</strong><p>{isAdmin ? "Full workshop, account, and repair management permissions." : "Assigned repair, status update, and customer feedback permissions."}</p></div></div>
      </section>
    </div>
  );
}

function ComplaintFormPage() {
  const [mode, setMode] = useState<"rating" | "complaint">("rating");
  const [repairId, setRepairId] = useState("FX-1048");
  const [rating, setRating] = useState(0);
  const [subject, setSubject] = useState("");
  const [message, setMessage] = useState("");
  const [submitted, setSubmitted] = useState(false);
  const [attachment, setAttachment] = useState("");
  const attachmentRef = useRef<HTMLInputElement>(null);
  const submitComplaint = (event: React.FormEvent) => {
    event.preventDefault();
    setSubmitted(true);
  };
  return (
    <div className="content">
      <PageHeading eyebrow="Customer feedback" title="Rate your repair or report a complaint" description="Share a rating when things went well, or tell us when something needs attention." />
      <form className="panel form-panel standalone-card" onSubmit={submitComplaint}>
        <div className="panel-head"><div><span className="kicker">Rate & complain</span><h2>What would you like to share?</h2><p>Choose one option to send feedback about your repair experience.</p></div><span className="step-badge"><Icon name={mode === "rating" ? "star" : "file"} size={16} /></span></div>
        <div className="feedback-mode">
          <button type="button" className={`rating-option ${mode === "rating" ? "active" : ""}`} onClick={() => { setMode("rating"); setSubmitted(false); }}><span><Icon name="star" /></span><div><strong>Rate the job</strong><p>Leave a star rating and optional feedback</p></div><i><Icon name="check" size={13} /></i></button>
          <button type="button" className={`complaint-option ${mode === "complaint" ? "active" : ""}`} onClick={() => { setMode("complaint"); setSubmitted(false); }}><span><Icon name="file" /></span><div><strong>Report a complaint</strong><p>Tell the workshop about a problem</p></div><i><Icon name="check" size={13} /></i></button>
        </div>
        <label className="field"><span>Related repair</span><select value={repairId} onChange={(event) => setRepairId(event.target.value)}><option value="FX-1048">FX-1048 · ASUS ROG Zephyrus G14</option><option value="FX-0982">FX-0982 · MacBook Pro 14-inch</option><option value="general">General complaint</option></select></label>
        {mode === "rating" ? <>
          <div className="rating-field"><span>How would you rate this repair?</span><div>{[1, 2, 3, 4, 5].map((value) => <button type="button" key={value} className={value <= rating ? "selected" : ""} onClick={() => setRating(value)} aria-label={`${value} star${value > 1 ? "s" : ""}`}><Icon name="star" size={27} /></button>)}<strong>{rating === 0 ? "Choose a rating" : ["", "Poor", "Fair", "Good", "Very good", "Excellent"][rating]}</strong></div></div>
          <label className="field"><span>Tell us more <small>Optional</small></span><textarea value={message} onChange={(event) => setMessage(event.target.value)} placeholder="What did the technician do well? What could improve?" /></label>
          <div className="form-actions"><p><Icon name="info" />Your feedback helps improve our workshop.</p><Button type="submit" icon="star" disabled={rating === 0}>{submitted ? "Rating submitted" : "Submit rating"}</Button></div>
          {submitted && <div className="submission-confirmation"><span><Icon name="check" /></span><div><strong>Thank you for your {rating}-star rating</strong><p>Your feedback has been shared with the workshop team.</p></div></div>}
        </> : <>
          <Field label="Complaint subject" value={subject} onChange={setSubject} placeholder="Briefly summarize the issue" />
          <label className="field"><span>What happened?</span><textarea value={message} onChange={(event) => setMessage(event.target.value)} placeholder="Describe your concern, what you expected, and how you would like us to help." /></label>
          <div className="upload-zone" onClick={() => attachmentRef.current?.click()} role="button" tabIndex={0}>
            <input ref={attachmentRef} type="file" accept="image/*,.pdf" onChange={(event) => setAttachment(event.target.files?.[0]?.name ?? "")} />
            <span><Icon name={attachment ? "check" : "upload"} /></span>
            <div><strong>{attachment || "Add supporting evidence"}</strong><p>{attachment ? "Attachment ready to send" : "Photo, screenshot, or PDF · Optional"}</p></div>
          </div>
          <div className="form-actions"><p><Icon name="clock" />We usually respond within one business day.</p><Button type="submit" icon="arrow" disabled={!subject || !message}>{submitted ? "Complaint submitted" : "Submit complaint"}</Button></div>
          {submitted && <div className="submission-confirmation"><span><Icon name="check" /></span><div><strong>We received your complaint</strong><p>Reference CMP-204. A workshop manager will contact you within one business day.</p></div></div>}
        </>}
      </form>
    </div>
  );
}

function MyRepairsList({ initialRepairId }: { initialRepairId?: string }) {
  const [expandedId, setExpandedId] = useState<string | null>(initialRepairId || "FX-1048");
  useEffect(() => {
    if (initialRepairId) setExpandedId(initialRepairId);
  }, [initialRepairId]);
  return (
    <section className="panel repair-list-panel">
      <div className="repair-list-head">
        <div><span className="kicker">Repair history</span><h2>Your devices</h2></div>
        <span>{customerRepairs.length} repairs</span>
      </div>
      <div className="repair-accordion">
        {customerRepairs.map((repair) => {
          const expanded = expandedId === repair.id;
          const stages = repair.method === "Mail-in"
            ? allStatuses.filter((stage) => stage !== "Pending")
            : allStatuses.filter((stage) => !["Package received", "Pending"].includes(stage));
          return (
            <article className={`repair-list-item ${expanded ? "expanded" : ""}`} key={repair.id}>
              <button className="repair-list-trigger" onClick={() => setExpandedId(expanded ? null : repair.id)} aria-expanded={expanded}>
                <span className="device-icon"><Icon name="computer" size={21} /></span>
                <span className="repair-list-primary"><small>{repair.id} · {repair.date}</small><strong>{repair.device}</strong><span>Serial: {repair.serial}</span></span>
                <span className="repair-list-method"><Icon name={repair.method === "Mail-in" ? "mail" : "map"} size={15} />{repair.method}</span>
                <StatusPill status={repair.status} />
                <span className="expand-icon"><Icon name="chevron" size={17} /></span>
              </button>
              {expanded && (
                <div className="repair-list-details">
                  <div className="timeline">
                    {stages.map((stage, index) => {
                      const active = index <= repair.activeStep;
                      return <div className={`timeline-step ${active ? "done" : ""} ${index === repair.activeStep && repair.status !== "Finished" ? "current" : ""}`} key={stage}><span className="timeline-node">{active ? <Icon name="check" size={13} /> : index + 1}</span><div><strong>{stage}</strong><small>{index < repair.activeStep ? "Completed" : index === repair.activeStep ? repair.status === "Finished" ? "Completed" : "In progress" : "Upcoming"}</small></div></div>;
                    })}
                  </div>
                  <div className="update-note"><Icon name="activity" /><div><strong>{repair.status === "Finished" ? "Final update" : "Latest update"}</strong><p>{repair.update}</p></div><span>{repair.status === "Finished" ? repair.date : "2h ago"}</span></div>
                </div>
              )}
            </article>
          );
        })}
      </div>
    </section>
  );
}

function RepairProgressCarousel() {
  const [activeIndex, setActiveIndex] = useState(0);
  const [isPaused, setIsPaused] = useState(false);
  const repair = customerRepairs[activeIndex];
  const stages = repair.method === "Mail-in"
    ? allStatuses.filter((stage) => stage !== "Pending")
    : allStatuses.filter((stage) => !["Package received", "Pending"].includes(stage));
  const goTo = (index: number) => setActiveIndex((index + customerRepairs.length) % customerRepairs.length);

  useEffect(() => {
    if (isPaused) return;
    const timer = window.setTimeout(() => goTo(activeIndex + 1), 7000);
    return () => window.clearTimeout(timer);
  }, [activeIndex, isPaused]);

  return (
    <section className="panel tracking-panel carousel-panel">
      <div className="carousel-toolbar">
        <div><span className="kicker">Repair progress</span><span className="carousel-count">{activeIndex + 1} of {customerRepairs.length}</span></div>
        <div className="carousel-controls">
          <button className="carousel-button previous" onClick={() => goTo(activeIndex - 1)} aria-label="Previous repair"><Icon name="arrow" /></button>
          <button className="carousel-button" onClick={() => goTo(activeIndex + 1)} aria-label="Next repair"><Icon name="arrow" /></button>
        </div>
      </div>
      <div className="carousel-content" key={repair.id}>
        <div className="panel-head"><div><span className="kicker">Repair #{repair.id.replace("FX-", "")}</span><h2>{repair.status === "Finished" ? "Repair completed" : "Repair in progress"}</h2></div><StatusPill status={repair.status} /></div>
        <div className="device-strip"><span className="device-icon"><Icon name="computer" size={24} /></span><div><strong>{repair.device}</strong><p>Serial: {repair.serial} · {repair.method} service</p></div><Button variant="ghost">View details</Button></div>
        <div className="timeline">
          {stages.map((stage, index) => {
            const active = index <= repair.activeStep;
            const current = index === repair.activeStep && repair.status !== "Finished";
            return <div className={`timeline-step ${active ? "done" : ""} ${current ? "current" : ""}`} key={stage}><span className="timeline-node">{active ? <Icon name="check" size={13} /> : index + 1}</span><div><strong>{stage}</strong><small>{index < repair.activeStep ? "Completed" : current ? "In progress" : repair.status === "Finished" ? "Completed" : "Upcoming"}</small></div></div>;
          })}
        </div>
        <div className="update-note"><Icon name="activity" /><div><strong>{repair.status === "Finished" ? "Final update" : "Latest update"}</strong><p>{repair.update}</p></div><span>{repair.status === "Finished" ? repair.date : "2h ago"}</span></div>
      </div>
      <div className="carousel-footer">
        <button className="autoplay-button" onClick={() => setIsPaused(!isPaused)}><Icon name={isPaused ? "play" : "pause"} size={14} />{isPaused ? "Resume auto-play" : "Stop auto-play"}</button>
        <div className={`carousel-dots ${isPaused ? "paused" : ""}`} aria-label="Choose repair">
          {customerRepairs.map((item, index) => <button key={item.id} className={index === activeIndex ? "active" : ""} onClick={() => goTo(index)} aria-label={`Show repair ${item.id}`}><span /></button>)}
        </div>
        <span className="carousel-footer-spacer" />
      </div>
    </section>
  );
}

function CustomerView({ page, onNavigate, linkedRepairId }: { page: CustomerPage; onNavigate: (page: CustomerPage) => void; linkedRepairId?: string }) {
  if (page === "new-repair") return <NewRepairPage onDone={() => onNavigate("repairs")} />;
  if (page === "profile") return <ProfilePage />;
  if (page === "complaint") return <ComplaintFormPage />;
  if (page === "repairs") return (
    <div className="content">
      <PageHeading eyebrow="Repair tracking" title="My repairs" description="Select a repair to expand its progress and latest workshop update." action={<Button icon="plus" onClick={() => onNavigate("new-repair")}>Start a repair</Button>} />
      <MyRepairsList initialRepairId={linkedRepairId} />
    </div>
  );

  return (
    <div className="content">
      <PageHeading eyebrow="Customer portal" title="Good morning, Maya" description="Start a new repair or check in on your current device." action={<Button icon="plus" onClick={() => onNavigate("new-repair")}>Start a repair</Button>} />
      <section className="summary-grid">
        <div className="summary-card featured"><span className="summary-icon"><Icon name="tool" /></span><div><p>Active repair</p><strong>Diagnosing</strong><small>ASUS ROG Zephyrus G14</small></div><Icon name="arrow" /></div>
        <div className="summary-card"><span className="summary-icon orange"><Icon name="clock" /></span><div><p>Estimated update</p><strong>Tomorrow</strong><small>By 5:00 PM</small></div></div>
        <div className="summary-card"><span className="summary-icon green"><Icon name="check" /></span><div><p>Repairs completed</p><strong>3 devices</strong><small>Since August 2023</small></div></div>
      </section>

      <RepairProgressCarousel />
    </div>
  );
}

function Metric({ label, value, note, icon, trend }: { label: string; value: string; note: string; icon: IconName; trend?: string }) {
  return <div className="metric-card"><div className="metric-top"><span><Icon name={icon} /></span>{trend && <small>{trend}</small>}</div><p>{label}</p><strong>{value}</strong><small>{note}</small></div>;
}

function RepairRequestsPage() {
  const [query, setQuery] = useState("");
  const [assignments, setAssignments] = useState<Record<string, string>>({});
  const [pageSize, setPageSize] = useState(4);
  const [currentPage, setCurrentPage] = useState(1);
  const filtered = repairs.filter((repair) => `${repair.customer} ${repair.device} ${repair.id}`.toLowerCase().includes(query.toLowerCase()));
  const pageCount = Math.max(1, Math.ceil(filtered.length / pageSize));
  const safePage = Math.min(currentPage, pageCount);
  const start = (safePage - 1) * pageSize;
  const visibleRepairs = filtered.slice(start, start + pageSize);
  return (
    <div className="content">
      <PageHeading eyebrow="Request management" title="Customer repair requests" description="Review incoming work, assign technicians, and monitor every request." action={<Button icon="plus">Create repair</Button>} />
      <section className="panel requests-panel">
        <div className="panel-head"><div><span className="kicker">Queue</span><h2>All requests</h2></div><div className="search-box"><Icon name="search" /><input value={query} onChange={(event) => { setQuery(event.target.value); setCurrentPage(1); }} placeholder="Search requests" /></div></div>
        <div className="table-wrap">
          <table>
            <thead><tr><th>Request</th><th>Device & issue</th><th>Delivery</th><th>Status</th><th>Technician</th><th /></tr></thead>
            <tbody>{visibleRepairs.map((repair) => <tr key={repair.id}>
              <td><strong>{repair.customer}</strong><small>{repair.id} · {repair.created}</small></td>
              <td><strong>{repair.device}</strong><small>{repair.issue}</small></td>
              <td><span className="method"><Icon name={repair.method === "Mail-in" ? "mail" : "map"} size={15} />{repair.method}</span></td>
              <td><StatusPill status={repair.status} /></td>
              <td><select value={assignments[repair.id] || repair.tech} onChange={(event) => setAssignments({ ...assignments, [repair.id]: event.target.value })}><option>Unassigned</option><option>Alex Kim</option><option>Sam Rivera</option><option>Priya Shah</option></select></td>
              <td><button className="row-action" aria-label={`View ${repair.id}`}><Icon name="arrow" /></button></td>
            </tr>)}</tbody>
          </table>
        </div>
        <div className="table-footer">
          <div className="page-size"><span>Rows per page</span><select value={pageSize} onChange={(event) => { setPageSize(Number(event.target.value)); setCurrentPage(1); }}><option value="2">2</option><option value="4">4</option><option value="10">10</option></select></div>
          <p>Showing {filtered.length === 0 ? 0 : start + 1}–{Math.min(start + pageSize, filtered.length)} of {filtered.length} requests</p>
          <div><Button variant="secondary" disabled={safePage === 1} onClick={() => setCurrentPage(Math.max(1, safePage - 1))}>Previous</Button><span className="page-number">{safePage} / {pageCount}</span><Button variant="secondary" disabled={safePage === pageCount} onClick={() => setCurrentPage(Math.min(pageCount, safePage + 1))}>Next</Button></div>
        </div>
      </section>
    </div>
  );
}

function AccountsPage() {
  const [showForm, setShowForm] = useState(false);
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [password, setPassword] = useState("");
  const [accountRole, setAccountRole] = useState<"Customer" | "Technician" | "Admin">("Customer");
  const [editingAccount, setEditingAccount] = useState<string | null>(null);
  const [editName, setEditName] = useState("");
  const [newPassword, setNewPassword] = useState("");
  const [editActive, setEditActive] = useState(true);
  const [accounts, setAccounts] = useState([
    { name: "Maya Chen", initials: "MC", detail: "maya.chen@example.com", meta: "3 repairs", role: "Customer", active: true },
    { name: "Jordan Blake", initials: "JB", detail: "jordan.blake@example.com", meta: "2 repairs", role: "Customer", active: true },
    { name: "Sofia Martinez", initials: "SM", detail: "sofia.m@example.com", meta: "1 repair", role: "Customer", active: false },
    { name: "Alex Kim", initials: "AK", detail: "Senior technician", meta: "7 active", role: "Technician", active: true },
    { name: "Sam Rivera", initials: "SR", detail: "Hardware specialist", meta: "5 active", role: "Technician", active: true },
    { name: "Priya Shah", initials: "PS", detail: "Repair technician", meta: "4 active", role: "Technician", active: true },
    { name: "Emma Walsh", initials: "EW", detail: "emma.walsh@fixsmith.com", meta: "Owner", role: "Admin", active: true },
    { name: "Daniel Cho", initials: "DC", detail: "daniel.cho@fixsmith.com", meta: "Manager", role: "Admin", active: true },
  ]);
  const submitAccount = (event: React.FormEvent) => {
    event.preventDefault();
    const initials = name.split(" ").map((word) => word[0]).join("").slice(0, 2).toUpperCase();
    setAccounts([...accounts, { name, initials, detail: email, meta: accountRole === "Customer" ? "0 repairs" : accountRole === "Technician" ? "0 active" : "Administrator", role: accountRole, active: true }]);
    setName("");
    setEmail("");
    setPassword("");
    setShowForm(false);
  };
  const openAccountEditor = (accountName: string) => {
    const account = accounts.find((item) => item.name === accountName);
    if (!account) return;
    setEditingAccount(accountName);
    setEditName(account.name);
    setEditActive(account.active);
    setNewPassword("");
  };
  const saveAccount = (event: React.FormEvent) => {
    event.preventDefault();
    setAccounts(accounts.map((account) => account.name === editingAccount ? { ...account, name: editName, initials: editName.split(" ").map((word) => word[0]).join("").slice(0, 2).toUpperCase(), active: editActive } : account));
    setEditingAccount(null);
    setNewPassword("");
  };
  const catalogs = [
    { role: "Customer", eyebrow: "Customers", title: "Customer accounts" },
    { role: "Technician", eyebrow: "Workshop team", title: "Technician accounts" },
    { role: "Admin", eyebrow: "Administration", title: "Admin accounts" },
  ];
  return (
    <div className="content">
      <PageHeading eyebrow="Account directory" title="All user accounts" description="Manage customers, technicians, and workshop administrators." action={<Button icon={showForm ? "chevron" : "plus"} onClick={() => setShowForm(!showForm)}>{showForm ? "Close form" : "Add account"}</Button>} />
      {showForm && <form className="panel account-form" onSubmit={submitAccount}>
        <div className="panel-head"><div><span className="kicker">New user</span><h2>Create an account</h2><p>Choose the correct access role before creating the account.</p></div></div>
        <div className="account-form-grid"><Field label="Full name" value={name} onChange={setName} placeholder="Enter full name" /><Field label="Email address" value={email} onChange={setEmail} placeholder="name@example.com" type="email" /><Field label="Temporary password" value={password} onChange={setPassword} placeholder="At least 8 characters" type="password" /><label className="field"><span>Account role</span><select value={accountRole} onChange={(event) => setAccountRole(event.target.value as "Customer" | "Technician" | "Admin")}><option>Customer</option><option>Technician</option><option>Admin</option></select></label></div>
        <div className="account-form-actions"><p><Icon name="info" />The user can change their temporary password after signing in.</p><Button type="submit" icon="plus" disabled={!name || !email || password.length < 8}>Create account</Button></div>
      </form>}
      <div className="account-catalogs">
        {catalogs.map((catalog) => {
          const members = accounts.filter((account) => account.role === catalog.role);
          return <section className="panel account-catalog" key={catalog.role}>
            <div className="panel-head"><div><span className="kicker">{catalog.eyebrow}</span><h2>{catalog.title}</h2></div><span className="count-badge">{members.length}</span></div>
            {members.map((account) => <div className={`account-row ${account.active ? "" : "inactive"}`} key={`${account.role}-${account.name}`}><div className="avatar">{account.initials}</div><div><strong>{account.name}</strong><small>{account.detail}</small></div><span className={`account-state ${account.active ? "active" : "inactive"}`}><i />{account.active ? account.meta : "Deactivated"}</span><button className="row-action" aria-label={`Edit ${account.name}`} onClick={() => openAccountEditor(account.name)}><Icon name="edit" /></button></div>)}
          </section>;
        })}
      </div>
      {editingAccount && <div className="modal-backdrop" role="presentation" onMouseDown={() => setEditingAccount(null)}>
        <section className="account-modal" role="dialog" aria-modal="true" aria-labelledby="account-modal-title" onMouseDown={(event) => event.stopPropagation()}>
          <div className="modal-head"><div><span className="kicker">Account management</span><h2 id="account-modal-title">Edit account</h2><p>Update login details or control access to this account.</p></div><button className="modal-close" onClick={() => setEditingAccount(null)} aria-label="Close account editor">×</button></div>
          <form onSubmit={saveAccount}>
            <div className="modal-account-summary"><div className="large-avatar">{editName.split(" ").map((word) => word[0]).join("").slice(0, 2).toUpperCase()}</div><div><strong>{editName}</strong><p>{accounts.find((account) => account.name === editingAccount)?.role} account</p></div><span className={`account-state ${editActive ? "active" : "inactive"}`}><i />{editActive ? "Active" : "Deactivated"}</span></div>
            <div className="modal-fields">
              <Field label="Account name" value={editName} onChange={setEditName} placeholder="Enter account name" />
              <Field label="New password" value={newPassword} onChange={setNewPassword} placeholder="Leave blank to keep current password" type="password" />
              <small>Password must contain at least eight characters. Leave this field empty if the password should not change.</small>
            </div>
            <div className={`access-control ${editActive ? "" : "deactivated"}`}><span><Icon name={editActive ? "check" : "info"} /><div><strong>Account access</strong><p>{editActive ? "This user can currently sign in." : "This user is blocked from signing in."}</p></div></span><Button variant="secondary" onClick={() => setEditActive(!editActive)}>{editActive ? "Deactivate account" : "Activate account"}</Button></div>
            <div className="modal-actions"><Button variant="secondary" onClick={() => setEditingAccount(null)}>Cancel</Button><Button type="submit" icon="check" disabled={!editName || (newPassword.length > 0 && newPassword.length < 8)}>Save changes</Button></div>
          </form>
        </section>
      </div>}
    </div>
  );
}

function ComplaintsPage() {
  const [selectedFeedback, setSelectedFeedback] = useState(feedbackRecords[0].id);
  return (
    <div className="content">
      <PageHeading eyebrow="Customer care" title="Ratings & complaints" description="Review star ratings and respond to complaints submitted from the customer feedback page." />
      <section className="complaint-layout">
        <div className="panel complaint-inbox">
          <div className="panel-head"><div><span className="kicker">Feedback inbox</span><h2>Customer submissions</h2></div><span className="nav-count">{feedbackRecords.length}</span></div>
          {feedbackRecords.map((feedback) => <button className={`complaint-row feedback-${feedback.type.toLowerCase()} ${selectedFeedback === feedback.id ? "selected" : ""}`} key={feedback.id} onClick={() => setSelectedFeedback(feedback.id)}>
            <span className={`avatar ${feedback.type === "Rating" ? "rating-avatar" : "complaint-avatar"}`}><Icon name={feedback.type === "Rating" ? "star" : "file"} size={16} /></span>
            <span><small>{feedback.type} · {feedback.id} · {feedback.repair}</small><strong>{feedback.subject}</strong><p>{feedback.message}</p></span>
            <span>{feedback.rating > 0 ? <span className="compact-stars">{Array.from({ length: 5 }, (_, index) => <span key={index} className={index < feedback.rating ? "filled" : ""}><Icon name="star" size={11} /></span>)}</span> : <StatusPill status={feedback.status} />}<small>{feedback.age}</small></span>
          </button>)}
        </div>
        <div className="panel complaint-detail">
          {feedbackRecords.filter((feedback) => feedback.id === selectedFeedback).map((feedback) => <div key={feedback.id}>
            <div className="panel-head"><div><span className="kicker">{feedback.type} · {feedback.id} · Repair {feedback.repair}</span><h2>{feedback.subject}</h2></div>{feedback.rating > 0 ? <div className="large-rating"><Icon name="star" />{feedback.rating}.0</div> : <StatusPill status={feedback.status} />}</div>
            <div className="complainant"><span className="avatar">{feedback.customer.split(" ").map((word) => word[0]).join("")}</span><div><strong>{feedback.customer}</strong><small>{feedback.type} submission · {feedback.age}</small></div></div>
            <div className={`complaint-message ${feedback.type === "Rating" ? "rating-message" : "complaint-message-alert"}`}><Icon name={feedback.type === "Rating" ? "star" : "file"} /><p>{feedback.message}</p></div>
            <label className="field"><span>{feedback.type === "Rating" ? "Thank the customer" : "Reply to customer"}</span><textarea placeholder={`Write a response to ${feedback.customer}...`} /></label>
            <div className="complaint-actions"><Button variant="secondary" icon="check">{feedback.type === "Rating" ? "Mark reviewed" : "Mark resolved"}</Button><Button icon="arrow">Send response</Button></div>
          </div>)}
        </div>
      </section>
    </div>
  );
}

function ManageTeamPage() {
  const technicians = [
    { id: "alex", name: "Alex Kim", initials: "AK", title: "Senior technician", capacity: 3 },
    { id: "sam", name: "Sam Rivera", initials: "SR", title: "Hardware specialist", capacity: 3 },
    { id: "priya", name: "Priya Shah", initials: "PS", title: "Repair technician", capacity: 3 },
    { id: "leo", name: "Leo Grant", initials: "LG", title: "Repair technician", capacity: 3 },
  ];
  const [jobs, setJobs] = useState([
    { id: "FX-1052", customer: "Avery Brooks", device: "HP Omen 16", issue: "Display flickers under load", status: "Pending", technician: null as string | null, interacted: false },
    { id: "FX-1051", customer: "Ethan Cole", device: "MSI Stealth 15", issue: "Fans run at maximum speed", status: "Pending", technician: null as string | null, interacted: false },
    { id: "FX-1050", customer: "Mia Davis", device: "Acer Predator Helios", issue: "Blue screen during gaming", status: "Pending", technician: "alex", interacted: false },
    { id: "FX-1048", customer: "Maya Chen", device: "ASUS ROG Zephyrus G14", issue: "Overheating and sudden shutdowns", status: "Diagnosing", technician: "alex", interacted: true },
    { id: "FX-1047", customer: "Jordan Blake", device: "Custom Desktop PC", issue: "No display after BIOS update", status: "Pending", technician: "sam", interacted: false },
    { id: "FX-1046", customer: "Noah Wilson", device: "Dell XPS 15 9520", issue: "Battery not charging", status: "Fixing", technician: "priya", interacted: true },
  ]);
  const [draggedId, setDraggedId] = useState<string | null>(null);
  const [dropTarget, setDropTarget] = useState<string | null>(null);
  const moveJob = (technician: string | null) => {
    if (!draggedId) return;
    setJobs(jobs.map((job) => job.id === draggedId && !job.interacted ? { ...job, technician } : job));
    setDraggedId(null);
    setDropTarget(null);
  };
  const renderJob = (job: typeof jobs[number]) => (
    <article
      className={`team-job ${job.interacted ? "locked" : "draggable"} ${draggedId === job.id ? "dragging" : ""}`}
      draggable={!job.interacted}
      onDragStart={(event) => {
        if (job.interacted) return;
        event.dataTransfer.effectAllowed = "move";
        event.dataTransfer.setData("text/plain", job.id);
        setDraggedId(job.id);
      }}
      onDragEnd={() => { setDraggedId(null); setDropTarget(null); }}
      key={job.id}
    >
      <span className="drag-handle"><Icon name={job.interacted ? "check" : "menu"} size={16} /></span>
      <div><span><small>{job.id}</small><StatusPill status={job.status} /></span><strong>{job.device}</strong><p>{job.issue}</p><small>{job.customer}</small></div>
      <span className={`interaction-state ${job.interacted ? "started" : ""}`}>{job.interacted ? "Work started" : "Not started"}</span>
    </article>
  );
  const unassigned = jobs.filter((job) => job.technician === null);
  return (
    <div className="content">
      <PageHeading eyebrow="Workshop staffing" title="Manage team workload" description="Drag untouched jobs between the unassigned queue and available technicians." />
      <div className="drag-guidance"><Icon name="info" /><p><strong>How reassignment works:</strong> Unassigned jobs and jobs marked “Not started” can be dragged. Work already started is locked to its technician.</p></div>
      <section className="team-board">
        <aside className={`panel unassigned-lane ${dropTarget === "unassigned" ? "drop-active" : ""}`} onDragOver={(event) => { event.preventDefault(); setDropTarget("unassigned"); }} onDragLeave={() => setDropTarget(null)} onDrop={(event) => { event.preventDefault(); moveJob(null); }}>
          <div className="team-lane-head"><div><span className="kicker">Dispatch queue</span><h2>Unassigned jobs</h2></div><span>{unassigned.length}</span></div>
          <div className="team-job-list">{unassigned.map(renderJob)}{unassigned.length === 0 && <div className="empty-drop"><Icon name="check" /><strong>Queue is clear</strong><p>Drag an untouched job here to unassign it.</p></div>}</div>
        </aside>
        <div className="technician-lanes">
          {technicians.map((technician) => {
            const technicianJobs = jobs.filter((job) => job.technician === technician.id);
            const isIdle = technicianJobs.length === 0;
            return <section className={`panel technician-lane ${isIdle ? "idle" : ""} ${dropTarget === technician.id ? "drop-active" : ""}`} key={technician.id} onDragOver={(event) => { event.preventDefault(); setDropTarget(technician.id); }} onDragLeave={() => setDropTarget(null)} onDrop={(event) => { event.preventDefault(); moveJob(technician.id); }}>
              <div className="technician-lane-head"><div className="avatar">{technician.initials}</div><div><span><strong>{technician.name}</strong>{isIdle && <small className="idle-badge">Idle</small>}</span><p>{technician.title}</p></div><span>{technicianJobs.length}/{technician.capacity}</span></div>
              {technicianJobs.length > 0 ? <div className="team-job-list">{technicianJobs.map(renderJob)}</div> : <div className="idle-dropzone"><Icon name="plus" /><strong>Available for work</strong><p>Drop an unassigned or untouched job here.</p></div>}
            </section>;
          })}
        </div>
      </section>
    </div>
  );
}

function AdminView({ page, onNavigate }: { page: AdminPage; onNavigate: (page: AdminPage) => void }) {
  if (page === "requests") return <RepairRequestsPage />;
  if (page === "accounts") return <AccountsPage />;
  if (page === "team") return <ManageTeamPage />;
  if (page === "complaints") return <ComplaintsPage />;
  if (page === "profile") return <StaffProfilePage role="admin" />;
  return (
    <div className="content">
      <PageHeading eyebrow="Operations overview" title="Workshop dashboard" description="Monitor repair flow, team capacity, and workshop performance." />
      <section className="metrics-grid">
        <Metric label="Active repairs" value="24" note="Across 4 technicians" icon="tool" trend="+8.2%" />
        <Metric label="New requests" value="4" note="2 mail-in, 2 drop-off" icon="inbox" trend="+12.5%" />
        <Metric label="Waiting for parts" value="6" note="2 delayed shipments" icon="box" />
        <Metric label="Completed this month" value="41" note="94% on-time completion" icon="check" trend="+16.4%" />
      </section>
      <section className="admin-grid">
        <div className="panel chart-panel">
          <div className="panel-head"><div><span className="kicker">Repair volume</span><h2>Weekly activity</h2></div><button className="period-button">Last 7 days <Icon name="chevron" size={14} /></button></div>
          <div className="chart">{[42, 63, 48, 82, 70, 92, 64].map((height, index) => <div className="bar-group" key={index}><div className="bar-track"><span style={{ height: `${height}%` }} /></div><small>{["Mon", "Tue", "Wed", "Thu", "Fri", "Sat", "Sun"][index]}</small></div>)}</div>
        </div>
        <div className="panel workload-panel">
          <div className="panel-head"><div><span className="kicker">Team capacity</span><h2>Technician workload</h2></div></div>
          {[["Alex Kim", "AK", 7, 78], ["Sam Rivera", "SR", 5, 56], ["Priya Shah", "PS", 4, 44]].map(([person, initials, count, width]) => <div className="workload-row" key={person}><div className="avatar small">{initials}</div><div><span><strong>{person}</strong><small>{count} active</small></span><div className="progress"><i style={{ width: `${width}%` }} /></div></div></div>)}
          <Button variant="secondary" onClick={() => onNavigate("team")}>Manage team</Button>
        </div>
      </section>
    </div>
  );
}

function AssignedRepairsPage({ initialRepairId }: { initialRepairId?: string }) {
  const assigned = repairs.filter((repair) => repair.tech === "Alex Kim");
  const [expandedId, setExpandedId] = useState<string | null>(initialRepairId || assigned[0]?.id || null);
  const [statuses, setStatuses] = useState<Record<string, string>>({ "FX-1048": "Diagnosing", "FX-1046": "Fixing" });
  const [notes, setNotes] = useState<Record<string, string>>({});
  const [savedId, setSavedId] = useState<string | null>(null);
  const saveUpdate = (repairId: string) => {
    setSavedId(repairId);
    window.setTimeout(() => setSavedId(null), 2200);
  };
  return (
    <div className="content">
      <PageHeading eyebrow="Technician queue" title="Assigned repairs" description="Select a repair to update its status and customer-facing note." action={<div className="date-chip"><Icon name="calendar" />Thursday, June 13</div>} />
      <section className="panel assigned-repair-list">
        <div className="repair-list-head"><div><span className="kicker">My assignments</span><h2>Active repair queue</h2></div><span>{assigned.length} repairs</span></div>
        {assigned.map((repair, index) => {
          const expanded = expandedId === repair.id;
          const status = statuses[repair.id] || repair.status;
          return <article className={`technician-repair-item ${expanded ? "expanded" : ""}`} key={repair.id}>
            <button className="technician-repair-trigger" onClick={() => setExpandedId(expanded ? null : repair.id)} aria-expanded={expanded}>
              <span className="job-order">{String(index + 1).padStart(2, "0")}</span>
              <span className="repair-list-primary"><small>{repair.id} · {repair.created}</small><strong>{repair.device}</strong><span><Icon name="profile" size={13} />{repair.customer} · {repair.method}</span></span>
              <span className="repair-issue-preview">{repair.issue}</span>
              <StatusPill status={status} />
              <span className="expand-icon"><Icon name="chevron" /></span>
            </button>
            {expanded && <div className="technician-update-form">
              <div className="issue-card"><span><Icon name="info" /></span><div><strong>Reported issue</strong><p>{repair.issue}. Review the customer’s description before recording your workshop update.</p></div></div>
              <div className="status-editor">
                <div className="section-label"><span>Update repair status</span><small>Visible to the customer immediately</small></div>
                <div className="status-options">{allStatuses.map((item) => <button key={item} className={status === item ? "active" : ""} onClick={() => setStatuses({ ...statuses, [repair.id]: item })}><span>{status === item && <Icon name="check" size={13} />}</span>{item}</button>)}</div>
              </div>
              <label className="field"><span>Customer update note</span><textarea value={notes[repair.id] || ""} onChange={(event) => setNotes({ ...notes, [repair.id]: event.target.value })} placeholder="Explain what you found, what work was completed, or what happens next..." /></label>
              <div className="workspace-actions"><span><Icon name="clock" />Last updated 2 hours ago</span><Button icon="check" onClick={() => saveUpdate(repair.id)}>{savedId === repair.id ? "Update saved" : "Save update"}</Button></div>
            </div>}
          </article>;
        })}
      </section>
    </div>
  );
}

function TechnicianWorkCarousel({ onOpenRepair }: { onOpenRepair: (repairId: string) => void }) {
  const unfinished = repairs.filter((repair) => repair.tech === "Alex Kim" && repair.status !== "Finished");
  const [activeIndex, setActiveIndex] = useState(0);
  const [isPaused, setIsPaused] = useState(false);
  const repair = unfinished[activeIndex];
  const goTo = (index: number) => setActiveIndex((index + unfinished.length) % unfinished.length);
  useEffect(() => {
    if (isPaused || unfinished.length < 2) return;
    const timer = window.setTimeout(() => goTo(activeIndex + 1), 7000);
    return () => window.clearTimeout(timer);
  }, [activeIndex, isPaused, unfinished.length]);
  return (
    <section className="panel tracking-panel carousel-panel technician-carousel">
      <div className="carousel-toolbar">
        <div><span className="kicker">Unfinished work</span><span className="carousel-count">{activeIndex + 1} of {unfinished.length}</span></div>
        <div className="carousel-controls"><button className="carousel-button previous" onClick={() => goTo(activeIndex - 1)} aria-label="Previous assignment"><Icon name="arrow" /></button><button className="carousel-button" onClick={() => goTo(activeIndex + 1)} aria-label="Next assignment"><Icon name="arrow" /></button></div>
      </div>
      <div className="carousel-content" key={repair.id}>
        <div className="workspace-head"><div><span className="kicker">{repair.id} · {repair.method}</span><h2>{repair.device}</h2><p>{repair.customer} · Serial {repair.serial}</p></div><StatusPill status={repair.status} /></div>
        <div className="technician-carousel-body">
          <div className="issue-card"><span><Icon name="info" /></span><div><strong>Reported issue</strong><p>{repair.issue}. This repair still requires technician action.</p></div></div>
          <div className="assignment-meta"><div><span>Customer</span><strong>{repair.customer}</strong></div><div><span>Received</span><strong>{repair.created}</strong></div><div><span>Service</span><strong>{repair.method}</strong></div></div>
          <div className="open-assignment"><p><Icon name="clock" />Open this assignment to update its status and customer note.</p><Button icon="arrow" onClick={() => onOpenRepair(repair.id)}>Open assigned repair</Button></div>
        </div>
      </div>
      <div className="carousel-footer">
        <button className="autoplay-button" onClick={() => setIsPaused(!isPaused)}><Icon name={isPaused ? "play" : "pause"} size={14} />{isPaused ? "Resume auto-play" : "Stop auto-play"}</button>
        <div className={`carousel-dots ${isPaused ? "paused" : ""}`}>{unfinished.map((item, index) => <button key={item.id} className={index === activeIndex ? "active" : ""} onClick={() => goTo(index)} aria-label={`Show assignment ${item.id}`}><span /></button>)}</div>
        <span className="carousel-footer-spacer" />
      </div>
    </section>
  );
}

function TechnicianComplaintsPage() {
  const assignedIds = repairs.filter((repair) => repair.tech === "Alex Kim").map((repair) => repair.id);
  const technicianFeedback = feedbackRecords.filter((feedback) => assignedIds.includes(feedback.repair));
  return (
    <div className="content">
      <PageHeading eyebrow="Customer care" title="Feedback on your repairs" description="Review ratings and complaints connected only to cases assigned to you." />
      <section className="panel technician-complaints">
        <div className="repair-list-head"><div><span className="kicker">Customer submissions</span><h2>Ratings & complaints</h2></div><span>{technicianFeedback.length} items</span></div>
        {technicianFeedback.map((feedback) => <article className={`technician-complaint feedback-${feedback.type.toLowerCase()}`} key={feedback.id}>
          <div className={`avatar ${feedback.type === "Rating" ? "rating-avatar" : "complaint-avatar"}`}><Icon name={feedback.type === "Rating" ? "star" : "file"} size={16} /></div>
          <div><span><small>{feedback.type} · {feedback.id} · Repair {feedback.repair}</small>{feedback.rating > 0 ? <span className="large-rating small"><Icon name="star" size={13} />{feedback.rating}.0</span> : <StatusPill status={feedback.status} />}</span><strong>{feedback.subject}</strong><p>{feedback.message}</p><small>From {feedback.customer} · {feedback.age}</small></div>
          <Button variant="secondary">{feedback.type === "Rating" ? "View feedback" : "View case"}</Button>
        </article>)}
      </section>
    </div>
  );
}

function TechnicianView({ page, linkedRepairId, onOpenRepair }: { page: TechnicianPage; linkedRepairId?: string; onOpenRepair: (repairId: string) => void }) {
  if (page === "assigned") return <AssignedRepairsPage initialRepairId={linkedRepairId} />;
  if (page === "complaints") return <TechnicianComplaintsPage />;
  if (page === "profile") return <StaffProfilePage role="technician" />;
  return (
    <div className="content">
      <PageHeading eyebrow="Technician workspace" title="Good morning, Alex" description="Focus on unfinished repairs currently assigned to you." action={<div className="date-chip"><Icon name="calendar" />Thursday, June 13</div>} />
      <section className="summary-grid tech-summary">
        <div className="summary-card featured"><span className="summary-icon"><Icon name="tool" /></span><div><p>Unfinished work</p><strong>2 repairs</strong><small>Both need attention today</small></div></div>
        <div className="summary-card"><span className="summary-icon orange"><Icon name="box" /></span><div><p>Waiting for parts</p><strong>0 repairs</strong><small>No delayed shipments</small></div></div>
        <div className="summary-card"><span className="summary-icon green"><Icon name="check" /></span><div><p>Completed this week</p><strong>9 repairs</strong><small>Avg. turnaround 2.4 days</small></div></div>
      </section>
      <TechnicianWorkCarousel onOpenRepair={onOpenRepair} />
    </div>
  );
}

function AuthPage({ mode, onModeChange, onLogin }: { mode: "login" | "register"; onModeChange: (mode: "login" | "register") => void; onLogin: (role: Role) => void }) {
  const [name, setName] = useState("");
  const [email, setEmail] = useState("");
  const [phone, setPhone] = useState("");
  const [password, setPassword] = useState("");
  const [confirmPassword, setConfirmPassword] = useState("");
  const isRegister = mode === "register";
  const valid = isRegister
    ? name.length > 1 && email.includes("@") && phone.length > 6 && password.length >= 8 && password === confirmPassword
    : email.includes("@") && password.length >= 1;
  const submit = (event: React.FormEvent) => {
    event.preventDefault();
    if (valid) onLogin("customer");
  };
  return (
    <main className="auth-shell">
      <section className="auth-story">
        <div className="auth-brand"><LogoMark /><span><strong>Fixsmith</strong><small>PC repair studio</small></span></div>
        <div className="auth-story-content">
          <span className="auth-eyebrow">Repair care, made clear</span>
          <h1>Your PC repair journey, all in one place.</h1>
          <p>Submit a repair, follow every update, and stay connected with the workshop from diagnosis to done.</p>
          <div className="auth-benefits">
            <div><span><Icon name="activity" /></span><div><strong>Live repair tracking</strong><p>See each stage as your device moves through the workshop.</p></div></div>
            <div><span><Icon name="tool" /></span><div><strong>Expert support</strong><p>Get clear notes directly from the technician handling your PC.</p></div></div>
            <div><span><Icon name="check" /></span><div><strong>Everything documented</strong><p>Keep requests, updates, and feedback together in one account.</p></div></div>
          </div>
        </div>
        <div className="auth-trust"><div className="auth-avatar-stack"><span>AK</span><span>SR</span><span>PS</span></div><p><strong>Trusted technicians</strong><br />Ready to help, six days a week</p></div>
      </section>
      <section className="auth-form-side">
        <div className="auth-mobile-brand"><LogoMark /><strong>Fixsmith</strong></div>
        <form className="auth-form" onSubmit={submit}>
          <div className="auth-form-heading"><span className="kicker">{isRegister ? "Create your account" : "Welcome back"}</span><h2>{isRegister ? "Get started with Fixsmith" : "Sign in to your account"}</h2><p>{isRegister ? "Create a customer account to request and track PC repairs." : "Enter your details to access your repair workspace."}</p></div>
          {isRegister && <Field label="Full name" value={name} onChange={setName} placeholder="Enter your full name" />}
          <Field label="Email address" value={email} onChange={setEmail} placeholder="name@example.com" type="email" />
          {isRegister && <Field label="Telephone number" value={phone} onChange={setPhone} placeholder="+1 (202) 555-0100" type="tel" />}
          <Field label={isRegister ? "Create password" : "Password"} value={password} onChange={setPassword} placeholder={isRegister ? "At least 8 characters" : "Enter your password"} type="password" />
          {isRegister && <Field label="Confirm password" value={confirmPassword} onChange={setConfirmPassword} placeholder="Enter your password again" type="password" />}
          {!isRegister && <div className="auth-options"><label><input type="checkbox" />Remember me</label><button type="button">Forgot password?</button></div>}
          {isRegister && password && confirmPassword && password !== confirmPassword && <p className="auth-error">Passwords do not match.</p>}
          <Button type="submit" icon="arrow" disabled={!valid}>{isRegister ? "Create customer account" : "Sign in"}</Button>
          <div className="auth-divider"><span>or</span></div>
          <p className="auth-switch">{isRegister ? "Already have an account?" : "New to Fixsmith?"}<button type="button" onClick={() => onModeChange(isRegister ? "login" : "register")}>{isRegister ? "Sign in" : "Create an account"}</button></p>
          {!isRegister && <div className="role-preview">
            <div><span className="kicker">Preview without login</span><p>Open any workspace instantly.</p></div>
            <div className="role-preview-buttons">
              <button type="button" onClick={() => onLogin("customer")}><Icon name="profile" /><span>Customer</span></button>
              <button type="button" onClick={() => onLogin("admin")}><Icon name="dashboard" /><span>Admin</span></button>
              <button type="button" onClick={() => onLogin("technician")}><Icon name="tool" /><span>Technician</span></button>
            </div>
          </div>}
        </form>
        <p className="auth-legal">By continuing, you agree to Fixsmith’s Terms of Service and Privacy Policy.</p>
      </section>
    </main>
  );
}

export default function App() {
  const [authenticated, setAuthenticated] = useState(false);
  const [authMode, setAuthMode] = useState<"login" | "register">("login");
  const [role, setRole] = useState<Role>("customer");
  const [customerPage, setCustomerPage] = useState<CustomerPage>("overview");
  const [adminPage, setAdminPage] = useState<AdminPage>("dashboard");
  const [technicianPage, setTechnicianPage] = useState<TechnicianPage>("workspace");
  const [linkedRepairId, setLinkedRepairId] = useState<string>();
  const [customerLinkedRepairId, setCustomerLinkedRepairId] = useState<string>();
  const [menuOpen, setMenuOpen] = useState(false);
  const [notificationOpen, setNotificationOpen] = useState(false);
  const openTechnicianRepair = (repairId: string) => { setLinkedRepairId(repairId); setTechnicianPage("assigned"); };
  const openCurrentProfile = () => {
    if (role === "customer") setCustomerPage("profile");
    if (role === "admin") setAdminPage("profile");
    if (role === "technician") setTechnicianPage("profile");
  };
  const logout = () => {
    setAuthenticated(false);
    setAuthMode("login");
  };
  const login = (selectedRole: Role) => {
    setRole(selectedRole);
    setAuthenticated(true);
    if (selectedRole === "customer") setCustomerPage("overview");
    if (selectedRole === "admin") setAdminPage("dashboard");
    if (selectedRole === "technician") setTechnicianPage("workspace");
  };
  const openNotificationTarget = (target: string, entityId?: string) => {
    if (role === "customer") {
      setCustomerLinkedRepairId(entityId);
      setCustomerPage(target as CustomerPage);
    } else if (role === "admin") {
      setAdminPage(target as AdminPage);
    } else {
      if (entityId) setLinkedRepairId(entityId);
      setTechnicianPage(target as TechnicianPage);
    }
  };
  const view = useMemo(() => role === "customer" ? <CustomerView page={customerPage} onNavigate={setCustomerPage} linkedRepairId={customerLinkedRepairId} /> : role === "admin" ? <AdminView page={adminPage} onNavigate={setAdminPage} /> : <TechnicianView page={technicianPage} linkedRepairId={linkedRepairId} onOpenRepair={openTechnicianRepair} />, [role, customerPage, adminPage, technicianPage, linkedRepairId, customerLinkedRepairId]);
  if (!authenticated) return <AuthPage mode={authMode} onModeChange={setAuthMode} onLogin={login} />;
  return <div className="app-shell"><Sidebar role={role} open={menuOpen} close={() => setMenuOpen(false)} customerPage={customerPage} onCustomerNavigate={setCustomerPage} adminPage={adminPage} onAdminNavigate={setAdminPage} technicianPage={technicianPage} onTechnicianNavigate={setTechnicianPage} onLogout={logout} /><NotificationDrawer role={role} open={notificationOpen} onClose={() => setNotificationOpen(false)} onNavigate={openNotificationTarget} /><div className="main-shell"><Header role={role} setRole={setRole} onMenu={() => setMenuOpen(true)} onProfile={openCurrentProfile} onLogout={logout} onNotifications={() => setNotificationOpen(true)} /><main>{view}</main><footer>© 2025 Fixsmith Repair Studio <span>Privacy · Terms · Service guarantee</span></footer></div></div>;
}
