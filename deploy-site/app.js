/**
 * Enterprise Recruitment - Careers Portal
 * Interactive Single Page Application
 */

// ==========================================
// MOCK DATA
// ==========================================

const JOBS_DATA = [
    {
        id: 1, title: "Senior Full Stack Engineer", slug: "senior-full-stack-engineer",
        department: "Engineering", department_id: 1, location: "San Francisco", location_id: 2,
        type: "full_time", type_label: "Full Time", arrangement: "Hybrid",
        experience: "5-8 years", salary: "$150,000 - $200,000/yr",
        featured: true, urgent: false, status: "published",
        summary: "We're looking for an experienced Full Stack Engineer to join our core platform team. You'll be building scalable web applications using modern technologies.",
        description: `<p>As a Senior Full Stack Engineer, you'll be a key contributor to our core platform, building scalable web applications that serve millions of users worldwide.</p>
        <p class="mt-4">You'll work closely with product managers, designers, and other engineers to deliver high-quality features and improvements to our platform.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Design and implement scalable backend services and APIs</li><li>Build responsive and performant frontend applications</li><li>Collaborate with cross-functional teams to define technical solutions</li><li>Mentor junior engineers and conduct code reviews</li><li>Optimize application performance and ensure high availability</li><li>Participate in architectural decisions and technical planning</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>5+ years of experience in full-stack development</li><li>Strong proficiency in React, Node.js, and TypeScript</li><li>Experience with cloud platforms (AWS, GCP, or Azure)</li><li>Deep understanding of database design and optimization</li><li>Experience with microservices architecture</li><li>Bachelor's degree in Computer Science or equivalent experience</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Competitive salary and equity package</li><li>Comprehensive health, dental, and vision insurance</li><li>Unlimited PTO policy</li><li>Annual learning and development budget</li><li>Flexible work arrangements</li><li>Home office stipend</li></ul>`,
        skills: ["React", "Node.js", "TypeScript", "AWS", "PostgreSQL", "Docker"],
        questions: [
            { id: 1, question: "Describe a complex technical challenge you solved recently.", type: "textarea", required: true },
            { id: 2, question: "Are you comfortable working in a hybrid environment?", type: "yes_no", required: true },
            { id: 3, question: "Years of experience with React", type: "number", required: true }
        ],
        published_at: "2025-06-15", closing_date: "2025-07-30", vacancies: 3
    },
    {
        id: 2, title: "Product Manager - Platform", slug: "product-manager-platform",
        department: "Product", department_id: 2, location: "New York", location_id: 1,
        type: "full_time", type_label: "Full Time", arrangement: "On-Site",
        experience: "4-6 years", salary: "$130,000 - $170,000/yr",
        featured: true, urgent: true, status: "published",
        summary: "Lead the strategy and execution of our platform product roadmap. Define product vision and work with engineering to deliver impactful features.",
        description: `<p>We're seeking an experienced Product Manager to lead our Platform team. You'll define the product roadmap, prioritize features, and work closely with engineering to deliver impactful solutions.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Define and communicate product vision and strategy</li><li>Create and maintain the product roadmap</li><li>Conduct user research and gather feedback</li><li>Work with engineering to prioritize and deliver features</li><li>Analyze product metrics and make data-driven decisions</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>4+ years of product management experience</li><li>Experience with B2B SaaS products</li><li>Strong analytical and problem-solving skills</li><li>Excellent communication and stakeholder management</li><li>Technical background preferred</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Competitive compensation package</li><li>Health and wellness benefits</li><li>Professional development budget</li><li>Stock options</li></ul>`,
        skills: ["Product Strategy", "Data Analysis", "User Research", "Agile", "SQL"],
        questions: [
            { id: 4, question: "Describe your approach to product prioritization.", type: "textarea", required: true },
            { id: 5, question: "Are you willing to relocate to New York?", type: "yes_no", required: true }
        ],
        published_at: "2025-06-20", closing_date: "2025-07-15", vacancies: 1
    },
    {
        id: 3, title: "Senior UX Designer", slug: "senior-ux-designer",
        department: "Design", department_id: 3, location: "Remote", location_id: 6,
        type: "full_time", type_label: "Full Time", arrangement: "Remote",
        experience: "4-7 years", salary: "$120,000 - $160,000/yr",
        featured: true, urgent: false, status: "published",
        summary: "Create beautiful, intuitive user experiences for our products. Lead design initiatives and mentor junior designers.",
        description: `<p>Join our Design team to create world-class user experiences. You'll work on complex problems and design solutions that delight our users.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Lead design projects from concept to implementation</li><li>Create wireframes, prototypes, and high-fidelity designs</li><li>Conduct user research and usability testing</li><li>Collaborate with product and engineering teams</li><li>Maintain and evolve our design system</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>4+ years of UX/UI design experience</li><li>Proficiency in Figma and design tools</li><li>Portfolio demonstrating strong design thinking</li><li>Experience with design systems</li><li>Understanding of accessibility standards</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Flexible remote work</li><li>Design conference budget</li><li>Latest design tools and hardware</li><li>Health benefits</li></ul>`,
        skills: ["Figma", "User Research", "Prototyping", "Design Systems", "HTML/CSS"],
        questions: [
            { id: 6, question: "Share a link to your portfolio or recent work.", type: "text", required: true },
            { id: 7, question: "Years of experience with design systems", type: "number", required: false }
        ],
        published_at: "2025-06-22", closing_date: "2025-08-15", vacancies: 2
    },
    {
        id: 4, title: "DevOps Engineer", slug: "devops-engineer",
        department: "Engineering", department_id: 1, location: "London", location_id: 3,
        type: "full_time", type_label: "Full Time", arrangement: "Hybrid",
        experience: "3-5 years", salary: "£70,000 - £95,000/yr",
        featured: false, urgent: false, status: "published",
        summary: "Build and maintain our cloud infrastructure. Automate deployments and ensure system reliability.",
        description: `<p>We're looking for a DevOps Engineer to help us build reliable, scalable infrastructure. You'll work with Kubernetes, AWS, and modern CI/CD pipelines.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Manage cloud infrastructure on AWS</li><li>Build and maintain CI/CD pipelines</li><li>Implement monitoring and alerting systems</li><li>Ensure security and compliance standards</li><li>Optimize infrastructure costs</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>3+ years of DevOps experience</li><li>Strong AWS or GCP experience</li><li>Kubernetes and containerization expertise</li><li>Terraform or CloudFormation experience</li><li>Scripting skills (Python, Bash)</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Competitive UK salary</li><li>Private health insurance</li><li>Pension scheme</li><li>Flexible working</li></ul>`,
        skills: ["AWS", "Kubernetes", "Docker", "Terraform", "CI/CD", "Python"],
        questions: [
            { id: 8, question: "Describe your experience with Kubernetes clusters.", type: "textarea", required: true }
        ],
        published_at: "2025-06-25", closing_date: "2025-08-30", vacancies: 2
    },
    {
        id: 5, title: "Data Scientist", slug: "data-scientist",
        department: "Data Science", department_id: 11, location: "San Francisco", location_id: 2,
        type: "full_time", type_label: "Full Time", arrangement: "Hybrid",
        experience: "3-5 years", salary: "$140,000 - $180,000/yr",
        featured: false, urgent: false, status: "published",
        summary: "Apply machine learning and statistical methods to solve real business problems. Build models that power our products.",
        description: `<p>Join our Data Science team to build machine learning models that power our products and drive business decisions.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Develop ML models for product features</li><li>Conduct exploratory data analysis</li><li>Build data pipelines and ETL processes</li><li>Present findings to stakeholders</li><li>Collaborate with engineering on model deployment</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>MS or PhD in Computer Science, Statistics, or related field</li><li>3+ years of industry experience in ML</li><li>Proficiency in Python, SQL, and ML frameworks</li><li>Experience with cloud ML platforms</li><li>Strong communication skills</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Research publication support</li><li>Conference attendance budget</li><li>Growth opportunities</li><li>Health benefits</li></ul>`,
        skills: ["Python", "Machine Learning", "SQL", "TensorFlow", "AWS", "Statistics"],
        questions: [
            { id: 9, question: "Describe a machine learning project you're most proud of.", type: "textarea", required: true },
            { id: 10, question: "Are you authorized to work in the US?", type: "yes_no", required: true }
        ],
        published_at: "2025-06-18", closing_date: "2025-09-15", vacancies: 2
    },
    {
        id: 6, title: "Sales Development Representative", slug: "sdr",
        department: "Sales", department_id: 5, location: "New York", location_id: 1,
        type: "full_time", type_label: "Full Time", arrangement: "On-Site",
        experience: "0-2 years", salary: "$60,000 - $80,000/yr + Commission",
        featured: false, urgent: true, status: "published",
        summary: "Start your sales career with us. Generate leads, qualify prospects, and help grow our customer base.",
        description: `<p>Join our Sales team as an SDR. You'll be the first point of contact for potential customers, qualifying leads and setting up meetings for our Account Executives.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Prospect and qualify leads</li><li>Conduct outbound outreach via email, phone, and social</li><li>Schedule meetings for Account Executives</li><li>Maintain CRM data accuracy</li><li>Hit monthly quota targets</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>0-2 years of sales or customer-facing experience</li><li>Strong communication skills</li><li>Self-motivated and goal-oriented</li><li>Familiarity with CRM tools</li><li>Bachelor's degree preferred</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Base salary + uncapped commission</li><li>Career progression to AE role</li><li>Sales training program</li><li>Health benefits</li></ul>`,
        skills: ["Sales", "CRM", "Communication", "Lead Generation", "Negotiation"],
        questions: [
            { id: 11, question: "Why are you interested in a sales career?", type: "textarea", required: true }
        ],
        published_at: "2025-06-28", closing_date: "2025-07-20", vacancies: 4
    },
    {
        id: 7, title: "Marketing Manager", slug: "marketing-manager",
        department: "Marketing", department_id: 4, location: "Berlin", location_id: 4,
        type: "full_time", type_label: "Full Time", arrangement: "Hybrid",
        experience: "4-6 years", salary: "€65,000 - €85,000/yr",
        featured: false, urgent: false, status: "published",
        summary: "Drive our marketing strategy in the DACH region. Lead campaigns and build brand awareness.",
        description: `<p>We're looking for a Marketing Manager to lead our DACH region marketing efforts. You'll develop and execute marketing strategies to drive growth.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Develop regional marketing strategy</li><li>Plan and execute marketing campaigns</li><li>Manage events and webinars</li><li>Collaborate with sales on lead generation</li><li>Analyze campaign performance</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>4+ years of B2B marketing experience</li><li>Fluent in German and English</li><li>Experience with marketing automation tools</li><li>Strong project management skills</li><li>Data-driven mindset</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Competitive salary</li><li>Relocation support</li><li>German language classes</li><li>Health insurance</li></ul>`,
        skills: ["Marketing Strategy", "German", "HubSpot", "Events", "Content Marketing"],
        questions: [
            { id: 12, question: "Describe a successful marketing campaign you led.", type: "textarea", required: true },
            { id: 13, question: "Fluency in German", type: "single_choice", required: true, options: ["Native", "Fluent (C1/C2)", "Intermediate (B1/B2)", "Basic"] }
        ],
        published_at: "2025-06-10", closing_date: "2025-08-01", vacancies: 1
    },
    {
        id: 8, title: "Customer Success Manager", slug: "customer-success-manager",
        department: "Customer Success", department_id: 6, location: "Toronto", location_id: 5,
        type: "full_time", type_label: "Full Time", arrangement: "Hybrid",
        experience: "3-5 years", salary: "$80,000 - $110,000/yr",
        featured: false, urgent: false, status: "published",
        summary: "Ensure our customers achieve their goals. Build relationships and drive product adoption.",
        description: `<p>As a Customer Success Manager, you'll be the trusted advisor to our customers, helping them maximize value from our platform.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Onboard and train new customers</li><li>Develop success plans for key accounts</li><li>Drive product adoption and engagement</li><li>Identify upsell and expansion opportunities</li><li>Collaborate with product on customer feedback</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>3+ years in Customer Success or Account Management</li><li>Experience with SaaS products</li><li>Strong relationship-building skills</li><li>Data-driven approach to customer health</li><li>Excellent communication skills</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Competitive salary</li><li>Health benefits</li><li>Flexible work</li><li>Growth opportunities</li></ul>`,
        skills: ["Customer Success", "SaaS", "CRM", "Data Analysis", "Communication"],
        questions: [
            { id: 14, question: "How do you handle a customer who is considering churning?", type: "textarea", required: true }
        ],
        published_at: "2025-06-12", closing_date: "2025-09-01", vacancies: 2
    },
    {
        id: 9, title: "QA Automation Engineer", slug: "qa-automation-engineer",
        department: "Quality Assurance", department_id: 12, location: "Remote", location_id: 6,
        type: "full_time", type_label: "Full Time", arrangement: "Remote",
        experience: "3-5 years", salary: "$100,000 - $140,000/yr",
        featured: false, urgent: false, status: "published",
        summary: "Build and maintain our test automation framework. Ensure product quality through automated testing.",
        description: `<p>Join our QA team to build robust test automation frameworks and ensure the highest quality of our products.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Design and implement test automation frameworks</li><li>Write automated tests for web and API</li><li>Integrate tests into CI/CD pipelines</li><li>Perform exploratory testing</li><li>Report and track bugs</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>3+ years of QA automation experience</li><li>Experience with Cypress or Playwright</li><li>Programming skills in JavaScript or Python</li><li>CI/CD integration experience</li><li>Understanding of testing methodologies</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Remote work flexibility</li><li>Learning budget</li><li>Health benefits</li><li>Equipment allowance</li></ul>`,
        skills: ["Cypress", "JavaScript", "CI/CD", "API Testing", "Selenium"],
        questions: [
            { id: 15, question: "What testing frameworks have you worked with?", type: "textarea", required: true }
        ],
        published_at: "2025-06-05", closing_date: "2025-08-15", vacancies: 2
    },
    {
        id: 10, title: "HR Business Partner", slug: "hr-business-partner",
        department: "Human Resources", department_id: 7, location: "New York", location_id: 1,
        type: "full_time", type_label: "Full Time", arrangement: "On-Site",
        experience: "5-8 years", salary: "$110,000 - $150,000/yr",
        featured: false, urgent: false, status: "published",
        summary: "Partner with leadership to drive people strategy. Support employee engagement and organizational development.",
        description: `<p>We're seeking an experienced HR Business Partner to support our Engineering and Product teams. You'll be a strategic partner to leadership.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Partner with business leaders on people strategy</li><li>Drive employee engagement initiatives</li><li>Support talent development and succession planning</li><li>Handle employee relations matters</li><li>Implement HR policies and programs</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>5+ years of HR experience in tech</li><li>SHRM or HRCI certification preferred</li><li>Strong interpersonal and coaching skills</li><li>Experience with HR analytics</li><li>Knowledge of employment law</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Competitive compensation</li><li>Health benefits</li><li>Professional development</li><li>Work-life balance</li></ul>`,
        skills: ["HR Strategy", "Employee Relations", "Coaching", "Analytics", "Employment Law"],
        questions: [
            { id: 16, question: "Describe your approach to employee engagement.", type: "textarea", required: true },
            { id: 17, question: "SHRM or HRCI certification", type: "yes_no", required: false }
        ],
        published_at: "2025-06-08", closing_date: "2025-10-15", vacancies: 1
    },
    {
        id: 11, title: "Backend Engineer - APIs", slug: "backend-engineer-apis",
        department: "Engineering", department_id: 1, location: "London", location_id: 3,
        type: "contract", type_label: "Contract", arrangement: "Remote",
        experience: "4-6 years", salary: "£500 - £700/day",
        featured: false, urgent: false, status: "published",
        summary: "Contract role: Design and build RESTful APIs and microservices for our platform.",
        description: `<p>We're looking for a contract Backend Engineer to help us build scalable APIs and microservices. This is a 6-month contract with potential extension.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Design and implement RESTful APIs</li><li>Build microservices architecture</li><li>Ensure API security and performance</li><li>Write comprehensive API documentation</li><li>Collaborate with frontend teams</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>4+ years of backend development</li><li>Strong Go or Python experience</li><li>Experience with gRPC and GraphQL</li><li>Kubernetes and Docker experience</li><li>Previous contract work preferred</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Competitive day rate</li><li>Remote work</li><li>Potential for extension</li><li>Flexible hours</li></ul>`,
        skills: ["Go", "Python", "REST API", "gRPC", "Kubernetes", "PostgreSQL"],
        questions: [
            { id: 18, question: "Availability start date", type: "text", required: true }
        ],
        published_at: "2025-06-27", closing_date: "2025-07-30", vacancies: 1
    },
    {
        id: 12, title: "Technical Writer", slug: "technical-writer",
        department: "Engineering", department_id: 1, location: "Remote", location_id: 6,
        type: "full_time", type_label: "Full Time", arrangement: "Remote",
        experience: "2-4 years", salary: "$80,000 - $110,000/yr",
        featured: false, urgent: false, status: "published",
        summary: "Create clear, comprehensive technical documentation for our developer platform and APIs.",
        description: `<p>Join our team as a Technical Writer to create world-class documentation for our developer platform. You'll make our complex technology accessible to developers worldwide.</p>`,
        responsibilities: `<ul class="list-disc list-inside space-y-2"><li>Write and maintain API documentation</li><li>Create developer guides and tutorials</li><li>Collaborate with engineers on technical content</li><li>Improve documentation based on user feedback</li><li>Maintain documentation site</li></ul>`,
        requirements: `<ul class="list-disc list-inside space-y-2"><li>2+ years of technical writing experience</li><li>Understanding of APIs and developer tools</li><li>Experience with Markdown and docs-as-code</li><li>Strong written communication skills</li><li>Basic programming knowledge</li></ul>`,
        benefits: `<ul class="list-disc list-inside space-y-2"><li>Fully remote</li><li>Health benefits</li><li>Learning budget</li><li>Flexible schedule</li></ul>`,
        skills: ["Technical Writing", "Markdown", "API Documentation", "Git", "Developer Tools"],
        questions: [
            { id: 19, question: "Share a link to documentation you've written.", type: "text", required: false },
            { id: 20, question: "Familiarity with APIs", type: "single_choice", required: true, options: ["Expert", "Advanced", "Intermediate", "Beginner"] }
        ],
        published_at: "2025-06-20", closing_date: "2025-08-30", vacancies: 1
    }
];

const DEPARTMENTS = [...new Set(JOBS_DATA.map(j => j.department))];
const LOCATIONS = [...new Set(JOBS_DATA.map(j => j.location))];

// ==========================================
// STATE
// ==========================================

let currentJobId = null;
let currentStep = 0;
const totalSteps = 5;

// ==========================================
// NAVIGATION
// ==========================================

function showSection(sectionId) {
    document.querySelectorAll('.page-section').forEach(s => s.classList.add('hidden'));
    const target = document.getElementById(sectionId);
    if (target) {
        target.classList.remove('hidden');
        window.scrollTo({ top: 0, behavior: 'smooth' });
    }
    // Update nav active states
    document.querySelectorAll('.nav-link').forEach(l => l.classList.remove('text-brand-700', 'active'));
    const activeLink = document.querySelector(`a[href="#${sectionId}"]`);
    if (activeLink) activeLink.classList.add('text-brand-700', 'active');
}

function initNavigation() {
    // Hash-based routing
    const hash = window.location.hash.slice(1) || 'home';
    if (hash.startsWith('job-')) {
        const jobId = parseInt(hash.split('-')[1]);
        if (jobId) showJobDetail(jobId);
    } else {
        showSection(hash);
    }

    window.addEventListener('hashchange', () => {
        const newHash = window.location.hash.slice(1) || 'home';
        if (newHash.startsWith('job-')) {
            const jobId = parseInt(newHash.split('-')[1]);
            if (jobId) showJobDetail(jobId);
        } else {
            showSection(newHash);
        }
    });

    // Mobile menu
    document.getElementById('mobile-menu-btn')?.addEventListener('click', () => {
        document.getElementById('mobile-menu').classList.toggle('hidden');
    });

    // Nav links smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(link => {
        link.addEventListener('click', (e) => {
            const href = link.getAttribute('href');
            if (href.length > 1) {
                const target = href.slice(1);
                if (!target.startsWith('job-')) {
                    showSection(target);
                }
            }
        });
    });
}

// ==========================================
// HOME PAGE
// ==========================================

function renderFeaturedJobs() {
    const container = document.getElementById('featured-jobs-grid');
    if (!container) return;
    const featured = JOBS_DATA.filter(j => j.featured).slice(0, 6);
    container.innerHTML = featured.map(job => `
        <div class="glass-card border border-gray-100 rounded-2xl p-6 hover:shadow-xl transition-all duration-300 hover:-translate-y-1">
            <div class="flex items-start justify-between mb-4">
                <div class="flex items-center space-x-3">
                    <div class="w-12 h-12 bg-brand-50 rounded-xl flex items-center justify-center text-brand-600 font-bold text-lg">${job.department[0]}</div>
                    <div>
                        <h3 class="font-semibold text-gray-900 hover:text-brand-700 transition"><a href="#job-${job.id}" onclick="showJobDetail(${job.id})">${job.title}</a></h3>
                        <p class="text-sm text-gray-500">${job.department}</p>
                    </div>
                </div>
                ${job.urgent ? '<span class="bg-red-50 text-red-600 text-xs font-medium px-2.5 py-1 rounded-full">Urgent</span>' : ''}
            </div>
            <div class="flex flex-wrap gap-2 mb-4">
                <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full flex items-center">
                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                    ${job.location}
                </span>
                <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full">${job.type_label}</span>
                <span class="bg-gray-100 text-gray-600 text-xs px-2.5 py-1 rounded-full">${job.arrangement}</span>
            </div>
            <div class="flex items-center justify-between pt-4 border-t border-gray-100">
                <span class="text-sm text-gray-500">${job.experience}</span>
                <span class="text-sm font-medium text-brand-700">${job.salary}</span>
            </div>
            <a href="#apply" onclick="showApplyForm(${job.id})" class="mt-4 block w-full text-center bg-brand-50 hover:bg-brand-100 text-brand-700 font-medium py-2.5 rounded-xl transition">Apply Now</a>
        </div>
    `).join('');
}

// ==========================================
// JOB LISTING
// ==========================================

function initJobFilters() {
    const deptSelect = document.getElementById('dept-filter');
    const locSelect = document.getElementById('loc-filter');
    if (deptSelect) {
        deptSelect.innerHTML = '<option value="">All Departments</option>' +
            DEPARTMENTS.map(d => `<option value="${d}">${d}</option>`).join('');
    }
    if (locSelect) {
        locSelect.innerHTML = '<option value="">All Locations</option>' +
            LOCATIONS.map(l => `<option value="${l}">${l}</option>`).join('');
    }
}

function filterJobs() {
    const search = document.getElementById('job-search')?.value.toLowerCase() || '';
    const dept = document.getElementById('dept-filter')?.value || '';
    const loc = document.getElementById('loc-filter')?.value || '';
    const type = document.getElementById('type-filter')?.value || '';

    const filtered = JOBS_DATA.filter(job => {
        if (search && !job.title.toLowerCase().includes(search) &&
            !job.department.toLowerCase().includes(search) &&
            !job.skills.some(s => s.toLowerCase().includes(search))) return false;
        if (dept && job.department !== dept) return false;
        if (loc && job.location !== loc) return false;
        if (type && job.type !== type) return false;
        return true;
    });

    renderJobList(filtered);
}

function resetFilters() {
    document.getElementById('job-search').value = '';
    document.getElementById('dept-filter').value = '';
    document.getElementById('loc-filter').value = '';
    document.getElementById('type-filter').value = '';
    renderJobList(JOBS_DATA);
}

function renderJobList(jobs) {
    const container = document.getElementById('jobs-list');
    const empty = document.getElementById('jobs-empty');
    const count = document.getElementById('job-count');
    const heroCount = document.getElementById('hero-job-count');

    if (count) count.textContent = jobs.length;
    if (heroCount) heroCount.textContent = JOBS_DATA.length;

    if (jobs.length === 0) {
        container.innerHTML = '';
        empty.classList.remove('hidden');
        return;
    }
    empty.classList.add('hidden');

    container.innerHTML = jobs.map(job => `
        <div class="job-card bg-white rounded-xl border border-gray-200 p-6">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div class="flex-1">
                    <div class="flex items-center gap-3 mb-2">
                        <h3 class="text-lg font-semibold text-gray-900 hover:text-brand-700 transition">
                            <a href="#job-${job.id}" onclick="showJobDetail(${job.id})">${job.title}</a>
                        </h3>
                        ${job.featured ? '<span class="bg-amber-50 text-amber-700 text-xs font-medium px-2 py-1 rounded-full">Featured</span>' : ''}
                        ${job.urgent ? '<span class="bg-red-50 text-red-600 text-xs font-medium px-2 py-1 rounded-full">Urgent</span>' : ''}
                    </div>
                    <div class="flex flex-wrap items-center gap-4 text-sm text-gray-500 mb-3">
                        <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>${job.department}</span>
                        <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${job.location}</span>
                        <span>${job.type_label}</span>
                        <span>${job.arrangement}</span>
                    </div>
                    <p class="text-gray-600 text-sm line-clamp-2">${job.summary}</p>
                </div>
                <div class="flex flex-col items-end gap-2 min-w-[140px]">
                    <span class="text-brand-700 font-semibold">${job.salary}</span>
                    <span class="text-sm text-gray-500">${job.experience}</span>
                    <a href="#apply" onclick="showApplyForm(${job.id})" class="bg-brand-600 text-white px-6 py-2 rounded-lg font-medium hover:bg-brand-700 transition text-sm text-center w-full">Apply Now</a>
                </div>
            </div>
        </div>
    `).join('');
}

// Search listener
document.addEventListener('DOMContentLoaded', () => {
    const searchInput = document.getElementById('job-search');
    if (searchInput) {
        searchInput.addEventListener('input', debounce(filterJobs, 300));
    }
});

function debounce(fn, ms) {
    let timeout;
    return (...args) => {
        clearTimeout(timeout);
        timeout = setTimeout(() => fn(...args), ms);
    };
}

// ==========================================
// JOB DETAIL
// ==========================================

function showJobDetail(jobId) {
    const job = JOBS_DATA.find(j => j.id === jobId);
    if (!job) return;

    showSection('job-detail');

    // Header
    document.getElementById('job-detail-header').innerHTML = `
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row md:items-center justify-between gap-4">
                <div>
                    <div class="flex items-center gap-3 mb-3 flex-wrap">
                        <span class="bg-white/20 backdrop-blur-sm text-white text-sm px-3 py-1 rounded-full">${job.department}</span>
                        <span class="bg-white/20 backdrop-blur-sm text-white text-sm px-3 py-1 rounded-full">${job.type_label}</span>
                        <span class="bg-white/20 backdrop-blur-sm text-white text-sm px-3 py-1 rounded-full">${job.arrangement}</span>
                    </div>
                    <h1 class="text-3xl md:text-4xl font-bold text-white mb-2">${job.title}</h1>
                    <div class="flex flex-wrap items-center gap-4 text-white/80 text-sm">
                        <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>${job.location}</span>
                        <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>${job.experience}</span>
                        <span class="flex items-center"><svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>${job.salary}</span>
                    </div>
                </div>
                <a href="#apply" onclick="showApplyForm(${job.id})" class="bg-white text-brand-700 px-8 py-3.5 rounded-xl font-semibold hover:bg-gray-100 transition shadow-lg text-center whitespace-nowrap">Apply for this Position</a>
            </div>
        </div>
    `;

    // Main content
    document.getElementById('job-detail-main').innerHTML = `
        ${job.description ? `<div class="bg-white rounded-xl border border-gray-200 p-8"><h2 class="text-xl font-bold text-gray-900 mb-4">About the Role</h2><div class="prose max-w-none text-gray-600">${job.description}</div></div>` : ''}
        ${job.responsibilities ? `<div class="bg-white rounded-xl border border-gray-200 p-8"><h2 class="text-xl font-bold text-gray-900 mb-4">Key Responsibilities</h2><div class="prose max-w-none text-gray-600">${job.responsibilities}</div></div>` : ''}
        ${job.requirements ? `<div class="bg-white rounded-xl border border-gray-200 p-8"><h2 class="text-xl font-bold text-gray-900 mb-4">Requirements</h2><div class="prose max-w-none text-gray-600">${job.requirements}</div></div>` : ''}
        ${job.benefits ? `<div class="bg-white rounded-xl border border-gray-200 p-8"><h2 class="text-xl font-bold text-gray-900 mb-4">Benefits & Perks</h2><div class="prose max-w-none text-gray-600">${job.benefits}</div></div>` : ''}
        <div class="bg-gradient-to-r from-brand-600 to-brand-700 rounded-xl p-8 text-center text-white">
            <h3 class="text-2xl font-bold mb-3">Interested in this role?</h3>
            <p class="text-white/80 mb-6">Take the first step towards your dream career.</p>
            <a href="#apply" onclick="showApplyForm(${job.id})" class="inline-block bg-white text-brand-700 px-8 py-3 rounded-xl font-semibold hover:bg-gray-100 transition">Apply Now</a>
        </div>
    `;

    // Sidebar
    document.getElementById('job-detail-sidebar').innerHTML = `
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Job Overview</h3>
            <div class="space-y-4">
                <div class="flex items-start"><svg class="w-5 h-5 text-brand-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg><div><p class="text-sm text-gray-500">Department</p><p class="font-medium text-gray-900">${job.department}</p></div></div>
                <div class="flex items-start"><svg class="w-5 h-5 text-brand-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg><div><p class="text-sm text-gray-500">Location</p><p class="font-medium text-gray-900">${job.location}</p></div></div>
                <div class="flex items-start"><svg class="w-5 h-5 text-brand-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div><p class="text-sm text-gray-500">Experience</p><p class="font-medium text-gray-900">${job.experience}</p></div></div>
                <div class="flex items-start"><svg class="w-5 h-5 text-brand-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div><p class="text-sm text-gray-500">Salary</p><p class="font-medium text-gray-900">${job.salary}</p></div></div>
                <div class="flex items-start"><svg class="w-5 h-5 text-brand-600 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg><div><p class="text-sm text-gray-500">Posted</p><p class="font-medium text-gray-900">${new Date(job.published_at).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p></div></div>
                ${job.closing_date ? `<div class="flex items-start"><svg class="w-5 h-5 text-red-500 mt-0.5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg><div><p class="text-sm text-gray-500">Apply Before</p><p class="font-medium text-red-600">${new Date(job.closing_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' })}</p></div></div>` : ''}
            </div>
        </div>
        ${job.skills.length ? `
        <div class="bg-white rounded-xl border border-gray-200 p-6">
            <h3 class="font-semibold text-gray-900 mb-4">Required Skills</h3>
            <div class="flex flex-wrap gap-2">
                ${job.skills.map(s => `<span class="bg-brand-50 text-brand-700 text-sm px-3 py-1.5 rounded-lg font-medium">${s}</span>`).join('')}
            </div>
        </div>` : ''}
    `;
}

// ==========================================
// APPLICATION FORM
// ==========================================

function showApplyForm(jobId) {
    const job = JOBS_DATA.find(j => j.id === jobId);
    if (!job) return;

    currentJobId = jobId;
    document.getElementById('apply-job-title').textContent = `Apply for ${job.title}`;
    document.getElementById('review-job-title').textContent = job.title;

    // Reset form
    currentStep = 0;
    document.getElementById('application-form').reset();
    updateStepIndicators();
    updateStepVisibility();

    showSection('apply');
}

function changeStep(direction) {
    const newStep = currentStep + direction;
    if (newStep < 0 || newStep >= totalSteps) return;

    // Validate current step
    if (direction > 0 && !validateStep(currentStep)) return;

    currentStep = newStep;
    updateStepIndicators();
    updateStepVisibility();
    window.scrollTo({ top: 0, behavior: 'smooth' });
}

function validateStep(step) {
    const stepEl = document.querySelector(`.form-step[data-step="${step}"]`);
    if (!stepEl) return true;

    const requiredFields = stepEl.querySelectorAll('[required]');
    let valid = true;

    requiredFields.forEach(field => {
        if (!field.value.trim()) {
            field.classList.add('border-red-500', 'ring-2', 'ring-red-200');
            valid = false;
        } else {
            field.classList.remove('border-red-500', 'ring-2', 'ring-red-200');
        }
    });

    if (!valid) {
        // Show validation message
        const existingMsg = stepEl.querySelector('.validation-msg');
        if (!existingMsg) {
            const msg = document.createElement('div');
            msg.className = 'validation-msg bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4';
            msg.textContent = 'Please fill in all required fields before proceeding.';
            stepEl.querySelector('.bg-white')?.insertBefore(msg, stepEl.querySelector('.bg-white').firstChild);
        }
    } else {
        const msg = stepEl.querySelector('.validation-msg');
        if (msg) msg.remove();
    }

    return valid;
}

function updateStepIndicators() {
    document.querySelectorAll('.step-ind').forEach((el, i) => {
        el.classList.remove('step-active', 'step-completed', 'step-pending');
        if (i === currentStep) el.classList.add('step-active');
        else if (i < currentStep) el.classList.add('step-completed');
        else el.classList.add('step-pending');
    });
}

function updateStepVisibility() {
    document.querySelectorAll('.form-step').forEach(el => {
        el.classList.remove('active');
        if (parseInt(el.dataset.step) === currentStep) el.classList.add('active');
    });

    document.getElementById('prev-btn').classList.toggle('hidden', currentStep === 0);
    document.getElementById('next-btn').classList.toggle('hidden', currentStep === totalSteps - 1);
    document.getElementById('submit-btn').classList.toggle('hidden', currentStep !== totalSteps - 1);
}

function updateFileLabel(input, labelId) {
    const label = document.getElementById(labelId);
    if (input.files && input.files[0]) {
        label.textContent = input.files[0].name;
        label.classList.add('text-brand-700', 'font-semibold');
    }
}

function submitApplication(e) {
    e.preventDefault();
    if (!validateStep(currentStep)) return;

    const job = JOBS_DATA.find(j => j.id === currentJobId);
    const appNumber = `APP-2026-${String(Math.floor(Math.random() * 900000) + 100000).slice(0, 6)}`;

    document.getElementById('thank-app-number').textContent = appNumber;
    document.getElementById('thank-app-date').textContent = new Date().toLocaleDateString('en-US', { month: 'long', day: 'numeric', year: 'numeric' });
    document.getElementById('thank-job-title').textContent = job ? job.title : 'the position';

    showSection('thank-you');
}

// ==========================================
// INITIALIZATION
// ==========================================

document.addEventListener('DOMContentLoaded', () => {
    initNavigation();
    renderFeaturedJobs();
    initJobFilters();
    renderJobList(JOBS_DATA);
});
